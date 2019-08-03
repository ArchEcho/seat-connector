<?php

namespace Warlof\Seat\Connector\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Warlof\Seat\Connector\Drivers\IUser;
use Warlof\Seat\Connector\Models\Log;
use Warlof\Seat\Connector\Models\User;

/**
 * Class DriverApplyPolicies.
 *
 * @package Warlof\Seat\Connector\Jobs
 */
class DriverApplyPolicies implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Warlof\Seat\Connector\Drivers\IClient
     */
    private $client;

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var bool
     */
    protected $terminator;

    /**
     * @var array
     */
    protected $tags = [
        'connector',
    ];

    /**
     * DriverApplyPolicies constructor.
     *
     * @param string $driver
     */
    public function __construct(string $driver, bool $terminator = false)
    {
        $this->driver     = $driver;
        $this->terminator = $terminator;
        $this->tags       = array_merge($this->tags, [$driver]);
    }

    /**
     * @return array
     */
    public function tags(): array
    {
        return $this->tags;
    }

    /**
     * Process the job.
     *
     * @throws \Warlof\Seat\Connector\Jobs\MissingDriverClientException
     * @throws \Seat\Services\Exceptions\SettingException
     */
    public function handle()
    {
        $config_key = sprintf('seat-connector.drivers.%s.client', $this->driver);
        $client = config($config_key);

        if (is_null($config_key) || ! class_exists($client))
            throw new MissingDriverClientException(sprintf('The client for driver %s is missing.', $this->driver));

        $this->client = $client::getInstance();

        $this->client->getSets();

        // collect all users from the active driver
        $users = $this->client->getUsers();

        // loop over each entity and apply policy
        foreach ($users as $user) {

            try {

                $this->applyPolicy($user);

            } catch (Exception $e) {

                $this->log('error', 'policy', sprintf('Unable to update the user %s. %s',
                        $user->getName(), $e->getMessage()));

            }

        }
    }

    /**
     * @param \Warlof\Seat\Connector\Drivers\IUser $user
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function applyPolicy(IUser $user)
    {
        $sets          = null;
        $new_nickname  = null;
        $profile       = User::where('connector_type', $this->driver)
                             ->where('connector_id', $user->getClientId())
                             ->first();

        // in case the user is unknown of SeAT; skip the process
        if (is_null($profile))
            return;

        // determine which nickname should be used by the user
        $expected_nickname = $profile->buildConnectorNickname();
        if ($user->getName() !== $expected_nickname)
            $new_nickname = $expected_nickname;

        $user_sets = $user->getSets();

        // collect all sets which are assigned to the user and determine if they are valid
        $pending_drops = $this->getDroppableSets($profile, $user_sets);

        // collect all valid sets for the current user
        $pending_adds = $this->getGrantableSets($profile, $user_sets);

        // check if there is a set to update
        $are_sets_outdated = $pending_adds->isNotEmpty() || $pending_drops->isNotEmpty();

        if ($are_sets_outdated)
            $this->updateUserSets($user, $profile, $pending_adds->toArray(), $pending_drops->toArray());

        // check if a nickname update is required
        if (! is_null($new_nickname))
            $this->updateUserProfile($user, $profile, $new_nickname);
    }

    /**
     * @param \Warlof\Seat\Connector\Models\User $profile
     * @param \Warlof\Seat\Connector\Drivers\ISet[] $sets
     * @return \Illuminate\Support\Collection
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function getDroppableSets(User $profile, array $sets)
    {
        $pending_drops = collect();

        foreach ($sets as $set) {
            if ($this->terminator || ! $profile->isAllowedSet($set->getId()))
                $pending_drops->push($set->getId());
        }

        return $pending_drops;
    }

    /**
     * @param \Warlof\Seat\Connector\Models\User $profile
     * @param \Warlof\Seat\Connector\Drivers\ISet[] $sets
     * @return \Illuminate\Support\Collection
     * @throws \Seat\Services\Exceptions\SettingException
     */
    private function getGrantableSets(User $profile, array $sets)
    {
        $pending_adds = collect();

        if ($this->terminator)
            return $pending_adds;

        $allowed_sets = $profile->allowedSets();

        foreach ($allowed_sets as $set_id) {
            if (! in_array($set_id, $sets))
                $pending_adds->push($set_id);
        }

        return $pending_adds;
    }

    /**
     * @param \Warlof\Seat\Connector\Drivers\IUser $user
     * @param \Warlof\Seat\Connector\Models\User $profile
     * @param array $pending_adds
     * @param array $pending_drops
     */
    private function updateUserSets(IUser $user, User $profile, array $pending_adds, array $pending_drops)
    {
        // drop all sets which have been marked for a removal
        foreach ($pending_drops as $set_id) {
            $set = $this->client->getSet($set_id);
            $user->removeSet($set);
        }

        // add all sets which have been marked for an addition
        foreach ($pending_adds as $set_id) {
            $set = $this->client->getSet($set_id);
            $user->addSet($set);
        }

        $this->log('info', 'policy',
            sprintf('Groups has successfully been updated for the user %s (%s) from group %d.',
                '', $user->getName(), $profile->group->id));
    }

    /**
     * @param \Warlof\Seat\Connector\Drivers\IUser $user
     * @param \Warlof\Seat\Connector\Models\User $profile
     * @param string $nickname
     */
    private function updateUserProfile(IUser $user, User $profile, string $nickname)
    {
        $user->setName($nickname);

        $profile->connector_name = $nickname;
        $profile->save();

        $this->log('info', 'policy',
            sprintf('Nickname from the user %s (%s) from group %d has been updated.',
                '', $user->getName(), $profile->group->id));
    }

    /**
     * @param string $level
     * @param string $category
     * @param string $message
     */
    private function log(string $level, string $category, string $message)
    {
        Log::create([
            'connector_type' => $this->driver,
            'level'          => $level,
            'category'       => $category,
            'message'        => $message,
        ]);
    }
}
