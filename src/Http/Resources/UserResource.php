<?php
/**
 * This file is part of seat-connector and provides user synchronization between both SeAT and third party platform
 *
 * Copyright (C) 2019  Loïc Leuilliot <loic.leuilliot@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Warlof\Seat\Connector\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class UserResource.
 *
 * @SWG\Definition(
 *     description="SeAT to Connector User mapping model",
 *     title="Connector User",
 *     type="object"
 * )
 *
 * @SWG\Property(
 *     type="integer",
 *     minimum=1,
 *     description="SeAT Group ID",
 *     property="group_id",
 *     example=1
 * )
 *
 * @SWG\Property(
 *     type="string",
 *     description="SeAT Connector driver",
 *     property="connector_type",
 *     example="discord"
 * )
 *
 * @SWG\Property(
 *     type="string",
 *     description="SeAT Connector entity ID for this driver",
 *     property="connector_id",
 *     example="133312047051046912"
 * )
 *
 * @SWG\Property(
 *     type="string",
 *     description="SeAT Connector entity name for this driver",
 *     property="connector_name",
 *     example="Demo User"
 * )
 *
 * @package Warlof\Seat\Connector\Http\Resources
 */
class UserResource extends Resource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'group_id'       => $this->group_id,
            'connector_type' => $this->connector_type,
            'connector_id'   => $this->connector_id,
            'connector_name' => $this->connector_name,
        ];
    }
}