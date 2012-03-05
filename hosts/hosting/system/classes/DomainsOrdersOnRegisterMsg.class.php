<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersOnRegisterMsg extends Message {
    public function __construct(array $params, $toUser) {
        parent::__construct('DomainsOrdersOnRegister', $toUser);

        $this->setParams($params);
    }

    public function getParams() {
        $Registrator = new Registrator();

        $IsSelected = $Registrator->Select((integer)$this->params['RegistratorID']);
        switch(ValueOf($IsSelected)){
          #-----------------------------------------------------------------------------
          case 'error':
            return ERROR | @Trigger_Error(500);
          case 'true':
            // For RegRu only
            if($Registrator->Settings['TypeID'] == 'RegRu'){
              #-------------------------------------------------------------------------
              $Domain = SprintF("%s.%s",$this->params['DomainName'],$this->params['Name']);
              #-------------------------------------------------------------------------
              $Result = $Registrator->GetUploadID($Domain);
              #-------------------------------------------------------------------------
              switch(ValueOf($Result)){
                case 'error':
                  return ERROR | @Trigger_Error(500);
                case 'array':
                  #---------------------------------------------------------------------
                  $UploadID = $Result['UploadID'];
                  #-------------------------------------------------------------------------
                  $this->params['UploadID']  =$UploadID;
                  #-------------------------------------------------------------------------
                  Debug($UploadID);
                  #-------------------------------------------------------------------------
                break;
                default:
                  return ERROR | @Trigger_Error(101);
              }
              #-------------------------------------------------------------------------
            }
          break;
          default:
            return ERROR | @Trigger_Error(101);
        }

        return $this->params;
     }
 }