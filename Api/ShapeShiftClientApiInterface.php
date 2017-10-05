<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Api;

interface ShapeShiftClientApiInterface
{
    /**
     * @param $withdrawAdd
     * @param $returnAdd
     * @param string $email
     * @param int $amount
     * @return mixed
     */
    public function Setup($withdrawAdd, $returnAdd, $email = "shapeshift@nepalbit.co.in", $amount = 0);

    /**
     * @param $depoAddress
     * @param int $amount
     * @return mixed
     */
    public function Set_Depo_Add($depoAddress, $amount = 0);

    /**
     * @return mixed
     */
    public function Rate();

    /**
     * @return mixed
     */
    public function Limit();

    /**
     * @return mixed
     */
    public function Rate_Limit();

    /**
     * @param int $max
     * @return mixed
     */
    public function RecentTx($max = 12);

    /**
     * @return mixed
     */
    public function TxStatus();

    /**
     * @return mixed
     */
    public function TimeRemaining();

    /**
     * @return mixed
     */
    public function Run();

}