<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
 class DomainsOrdersOnTransferMsg extends Message {
     public function __construct(array $params, $toUser) {
         parent::__construct('DomainsOrdersOnTransfer', $toUser, $params);
     }

     public function getParams() {
         # Достаем registrar и его префикс
         if (Preg_Match('/registrar:\s+((\w+|-)+)/', $this->params['WhoIs'], $String)) {
             $Registrar = Next($String);
             if (Preg_Match('/((\w+|-)+)-REG/', $Registrar, $String))
                 $PrefixRegistrar = Next($String);
         }

        # Получаем параметры для формирования Event
        $Where = SPrintF('`OrderID` = %s', $this->params['OrderID']);
        $UserID = DB_Select('DomainsOrdersOwners', 'UserID as ID', Array('UNIQ', 'Where' => $Where));
        $SchemeName = DB_Select('DomainsSchemes', 'Name as SchemeName', Array('UNIQ', 'ID' => $this->params['SchemeID']));
        #-------------------------------------------------------------------------------
        if(IsSet($Registrar) && IsSet($PrefixRegistrar)){
          $this->params['registrar'] = $Registrar;
          $this->params['prefixRegistrar'] = $PrefixRegistrar;

          # Получаем параметры регистратора к которому осуществляется трансфер
          $RegistratorID = DB_Select('DomainsSchemes','RegistratorID as ID',Array('UNIQ','ID'=>$this->params['SchemeID']));
          $Settings = DB_Select('Registrators','*',Array('UNIQ','ID'=>$RegistratorID['ID']));
          #-----------------------------------------------------------------------------
          Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrators[TypeID] - " . $Settings['TypeID']);
          Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Settings[PrefixNic] - " . $Settings['PrefixNic']);
          Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrar - " . $Registrar);
          Debug("[Notifies/Email/DomainsOrdersOnTransfer]: PrefixRegistrar - " . $PrefixRegistrar);
          #-----------------------------------------------------------------------------
          # Проверяем является ли регистрар нашим регистратором
          if($PrefixRegistrar == $Settings['PrefixNic']){
            $this->params['internalRegister'] = true;
            #---------------------------------------------------------------------------
            Debug("[Notifies/Email/DomainsOrdersOnTransfer]: IsOurRegistrar - TRUE");
            Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Инструкция по трансферу в пределах регистратора");
            #---------------------------------------------------------------------------
            $this->params['registratorID'] = $Settings['TypeID'];
            $this->params['partnerLogin'] = $Settings['PartnerLogin'];
            $this->params['partnerContract'] = $Settings['PartnerContract'];

            # Достаем статью с информацией о шаблонах документов и контактами регистратора
            $Where = SPrintF('`Partition`=\'Registrators/%s/internal\'', $PrefixRegistrar);
            $Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>$Where));
            switch(ValueOf($Clause)) {
              case 'array':
                $TransferDoc = trim(Strip_Tags($Clause['Text']));

                break;
              default:
                #-----------------------------------------------------------------------
                Debug(SPrintF('[Notifies/Email/DomainsOrdersOnTransfer]: Статья не найдена. Ожидалась Registrators/%s/internal', $PrefixRegistrar));
                #-----------------------------------------------------------------------
                $TransferDoc = "Для получения информации об оформлении писем Вашему текущему регистратору и его контактах перейдите на его сайт.";
                #-----------------------------------------------------------------------
                # Уведомление об ошибке статьи
                $Event = Array(
                    'UserID'    => $UserID['ID'],
                    'PriorityID'=> 'Error',
                    'Text'      => SPrintF('Статья по переносу домена не найдена. Ожидалась Registrators/%s/internal',$PrefixRegistrar),
                    'IsReaded'  => FALSE
                );

                $Event = Comp_Load('Events/EventInsert', $Event);
                if(!$Event)
                  return ERROR | @Trigger_Error(500);
            }

            $this->params['transferDoc'] = $TransferDoc;
          }
          else {
            $this->params['internalRegister'] = false;
            #---------------------------------------------------------------------------
            Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Инструкция по трансферу от стороннего регистратора");
            #---------------------------------------------------------------------------
            # Формируем постфикс идентификатора
            switch ($SchemeName['SchemeName']){
              case 'su':
                $PostfixNic = 'FID';
              break;
              case 'рф':
                $PostfixNic = 'RF';
              break;
              default:
                $PostfixNic = 'RIPN';
            }

            $this->params['registrar'] = $Registrar;
            $this->params['registratorID'] = $Settings['TypeID'];
            $this->params['jurName'] = $Settings['JurName'];
            $this->params['prefixNic'] = $Settings['PrefixNic'];
            $this->params['postfixNic'] = $PostfixNic;
            $this->params['schemeName'] = strtoupper($SchemeName['SchemeName']);

            # Достаем статью с информацией о шаблонах документов и контактами регистратора
            $Where = SPrintF('`Partition`=\'Registrators/%s/external\'',$PrefixRegistrar);
            $Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>$Where));
            switch(ValueOf($Clause)){
              case 'array':
                $TransferDoc = trim(Strip_Tags($Clause['Text']));
                break;
              default:
                #-----------------------------------------------------------------------
                Debug(SPrintF('[Notifies/Email/DomainsOrdersOnTransfer]: Статья не найдена. Ожидалась Registrators/%s/external',$PrefixRegistrar));
                #-----------------------------------------------------------------------
                $TransferDoc = "\n\nДля получения информации об оформлении писем Вашему текущему регистратору и его контактах перейдите на его сайт.";
                #-----------------------------------------------------------------------
                # Уведомление об ошибке статьи
            $Event = Array(
                    'UserID'	=> $UserID['ID'],
                    'PriorityID'	=> 'Error',
                    'Text'		=> SPrintF('Статья по переносу домена не найдена. Ожидалась Registrators/%s/external',$PrefixRegistrar),
                    'IsReaded'	=> FALSE
                          );
                $Event = Comp_Load('Events/EventInsert',$Event);
                if(!$Event)
                  return ERROR | @Trigger_Error(500);
                #-----------------------------------------------------------------------
            }

            $this->params['transferDoc'] = $TransferDoc;
          }
        }
        else {
          Debug("[Notifies/Email/DomainsOrdersOnTransfer]: Registrar или PrefixRegistrar не был определён.");

          # Уведомление об ошибке формирования инструкции
          $Event = Array (
                'UserID'    => $UserID['ID'],
                'PriorityID'=> 'Error',
                'Text'      => SPrintF('Ошибка автоматического формирования инструкции по переносу домена (%s.%s) к нам.', $this->params['DomainName'], $SchemeName['SchemeName'] /*$this->params['SchemeName']*/),
                'IsReaded'  => FALSE
          );

          $Event = Comp_Load('Events/EventInsert', $Event);
          if(!$Event)
            return ERROR | @Trigger_Error(500);
        }

        return $this->params;
     }
 }
