<?php

/**
 * Domain registrator. Everyone domain registrators should be implement this interface.
 *
 * @author vvelikodny
 */
interface Registrator1 {
    /**
     * Registers domain.
     *
     * @return void
     */
    public function DomainRegister($settings, $domainName, $domainZone, $years, $ns1Name, $ns1IP, $ns2Name, $ns2IP,
        $ns3Name, $ns3IP, $ns4Name, $ns4IP, $contractID, $isPrivateWhoIs, $personID, $person);

    /**
     * @abstract
     * @return void
     */
    public function GetUploadID();

    /**
     * @abstract
     * @return void
     */
    public function DomainProlong();

    /**
     *
     *
     * @return void
     */
    public function DomainNsChange();

    /**
     * Check if task completed.
     *
     * @param $settings
     * @param $ticketId Ticket id to be checked.
     * @return void
     */
    public function CheckTask($settings, $ticketId);

    /**
     * @abstract
     * @return void
     */
    public function ContractRegister();

    /**
     * @abstract
     * @return void
     */
    public function GetContract();
}
?>