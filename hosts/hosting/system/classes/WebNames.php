<?php

/**
 * Represent WebNames domain registrator.
 *
 * @author vvelikodny
 */
class WebNames implements Registrator {
    /** Default registrator query charset. */
    private const DFLT_CHARSET = "CP1251";

    /** {@inheritDoc} */
    public function DomainRegister($settings, $domainName, $domainZone, $years, $ns1Name, $ns1IP, $ns2Name, $ns2IP,
            $ns3Name, $ns3IP, $ns4Name, $ns4IP, $contractID, $isPrivateWhoIs, $personID, $person) {
        $__args_types = Array('array','string','string','integer','string','string','string','string','string','string','string','string','boolean','string','string','array');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $http = Array(
            'Address'  => $settings['Address'],
            'Port'     => $settings['Port'],
            'Host'     => $settings['Address'],
            'Protocol' => $settings['Protocol'],
            'Charset'  => 'CP1251'
        );

        $domain = SPrintF('%s.%s' ,$domainName, $domainZone);

        $query = Array(
            'thisPage'           => 'pispRegistration',
            'username'           => $settings['Login'],
            'password'           => $settings['Password'],
            'domain_name'        => $domain,
            'interface_revision' => 1,
            'interface_lang'     => 'en'
        );

        $query['period'] = $years;

        if (In_Array($domainZone,Array('ru','su'))) {
            switch ($personID) {
                case 'Natural': {
                    $query['person']         = SPrintF('%s %s %s',Translit($person['Name']),Mb_SubStr(Translit($person['Lastname']),0,1),Translit($person['Sourname']));
                    $query['private_person'] = ($isPrivateWhoIs?'1':'0');
                    $query['person_r']       = SPrintF('%s %s %s',$person['Sourname'],$person['Name'],$person['Lastname']);
                    $query['passport']       = SPrintF('%s %s выдан %s %s',$person['PasportLine'],$person['PasportNum'],$person['PasportWhom'],$person['PasportDate']);
                    $query['residence']      = SPrintF('%s, %s, %s, %s',$person['pIndex'],$person['pState'],$person['pCity'],$person['pAddress']);
                    $query['birth_date']     = $person['BornDate'];
                    $query['country']        = $person['pCountry'];
                    $query['p_addr']         = SPrintF('%s, %s, %s, %s, %s',$person['pIndex'],$person['pState'],$person['pCity'],$person['pAddress'],$person['pRecipient']);
                    $query['phone']          = $person['Phone'];
                    $query['fax']            = $person['Fax'];
                    $query['e_mail']         = $person['Email'];

                    break;
                }
                case 'Juridical': {
                    $query['org']       = SPrintF('%s %s',translit($person['CompanyName']),translit($person['CompanyForm']));
                    $query['org_r']     = SPrintF('%s "%s"',$person['CompanyForm'],$person['CompanyName']);
                    $query['code']      = $person['Inn'];
                    $query['kpp']       = $person['Kpp'];
                    $query['country']   = $person['jCountry'];
                    $query['address_r'] = SPrintF('%s, %s, %s, %s',$person['jIndex'],$person['pState'],$person['jCity'],$person['jAddress']);
                    $query['p_addr']    = SPrintF('%s, %s, %s, %s, %s, %s "%s"',$person['pIndex'],$person['pState'],$person['pCountry'],$person['pCity'],$person['pAddress'],$person['CompanyForm'],$person['CompanyName']);
                    $query['phone']     = $person['Phone'];
                    $query['fax']       = $person['Fax'];
                    $query['e_mail']    = $person['Email'];

                    break;
                }
                default:
                    return new gException('WRONG_PROFILE_ID','Неверный идентификатор профиля');
            }
        }
        else {
        switch ($personID) {
            case 'Natural': {
                $query['o_company'] = 'Private person';
                $query['a_company'] = 'Private person';
                $query['t_company'] = 'Private person';
                $query['b_company'] = 'Private person';

                $query['o_country_code'] = $person['pCountry'];
                $query['a_country_code'] = $person['pCountry'];
                $query['t_country_code'] = $person['pCountry'];
                $query['b_country_code'] = $person['pCountry'];

                $query['o_postcode'] = $person['pIndex'];
                $query['a_postcode'] = $person['pIndex'];
                $query['t_postcode'] = $person['pIndex'];
                $query['b_postcode'] = $person['pIndex'];

                $query['o_first_name'] = Translit($person['Name']);
                $query['a_first_name'] = Translit($person['Name']);
                $query['t_first_name'] = Translit($person['Name']);
                $query['b_first_name'] = Translit($person['Name']);

                $query['o_last_name'] = Translit($person['Sourname']);
                $query['a_last_name'] = Translit($person['Sourname']);
                $query['t_last_name'] = Translit($person['Sourname']);
                $query['b_last_name'] = Translit($person['Sourname']);

                $query['o_email'] = $person['Email'];
                $query['a_email'] = $person['Email'];
                $query['t_email'] = $person['Email'];
                $query['b_email'] = $person['Email'];

                $query['o_addr'] = Translit($person['pAddress']);
                $query['a_addr'] = Translit($person['pAddress']);
                $query['t_addr'] = Translit($person['pAddress']);
                $query['b_addr'] = Translit($person['pAddress']);

                $query['o_city'] = Translit($person['pCity']);
                $query['a_city'] = Translit($person['pCity']);
                $query['t_city'] = Translit($person['pCity']);
                $query['b_city'] = Translit($person['pCity']);

                $query['o_state'] = Translit($person['pState']);
                $query['a_state'] = Translit($person['pState']);
                $query['t_state'] = Translit($person['pState']);
                $query['b_state'] = Translit($person['pState']);

                break;
            }
            case 'Juridical': {
                $companyEn = SPrintF('%s %s', Translit($person['CompanyName']), Translit($person['CompanyForm']));

                $query['o_company'] = $companyEn;
                $query['a_company'] = $companyEn;
                $query['t_company'] = $companyEn;
                $query['b_company'] = $companyEn;

                $query['o_country_code'] = $person['jCountry'];
                $query['a_country_code'] = $person['jCountry'];
                $query['t_country_code'] = $person['jCountry'];
                $query['b_country_code'] = $person['jCountry'];

                $query['o_postcode'] = $person['jIndex'];
                $query['a_postcode'] = $person['jIndex'];
                $query['t_postcode'] = $person['jIndex'];
                $query['b_postcode'] = $person['jIndex'];

                $query['o_first_name'] = Translit($person['dName']);
                $query['a_first_name'] = Translit($person['dName']);
                $query['t_first_name'] = Translit($person['dName']);
                $query['b_first_name'] = Translit($person['dName']);

                $query['o_last_name'] = Translit($person['dSourname']);
                $query['a_last_name'] = Translit($person['dSourname']);
                $query['t_last_name'] = Translit($person['dSourname']);
                $query['b_last_name'] = Translit($person['dSourname']);

                $query['o_email'] = $person['Email'];
                $query['a_email'] = $person['Email'];
                $query['t_email'] = $person['Email'];
                $query['b_email'] = $person['Email'];

                $query['o_addr'] = Translit($person['jAddress']);
                $query['a_addr'] = Translit($person['jAddress']);
                $query['t_addr'] = Translit($person['jAddress']);
                $query['b_addr'] = Translit($person['jAddress']);

                $query['o_city'] = Translit($person['jCity']);
                $query['a_city'] = Translit($person['jCity']);
                $query['t_city'] = Translit($person['jCity']);
                $query['b_city'] = Translit($person['jCity']);

                $query['o_state'] = Translit($person['jState']);
                $query['a_state'] = Translit($person['jState']);
                $query['t_state'] = Translit($person['jState']);
                $query['b_state'] = Translit($person['jState']);

                break;
            }
            default:
                return new gException('WRONG_PERSON_TYPE_ID','Неверный идентификатор типа персоны');
        }

        $phone = $person['Phone'];

        if ($phone) {
            $phone = Preg_Split('/\s+/',$phone);

            $phone = SPrintF('%s.%s%s',Current($phone),Next($phone),Next($phone));

            $query['o_phone'] = $phone;
            $query['a_phone'] = $phone;
            $query['t_phone'] = $phone;
            $query['b_phone'] = $phone;
        }
        else {
            $query['o_phone'] = '';
            $query['a_phone'] = '';
            $query['t_phone'] = '';
            $query['b_phone'] = '';
        }
        #---------------------------------------------------------------------------
        $fax = $query['Fax'];
        #---------------------------------------------------------------------------
        if($fax){
          #-------------------------------------------------------------------------
          $fax = Preg_Split('/\s+/',$fax);
          #-------------------------------------------------------------------------
          $fax = SPrintF('%s.%s%s',Current($fax),Next($fax),Next($fax));
          #-------------------------------------------------------------------------
          $query['o_fax'] = $fax;
          $query['a_fax'] = $fax;
          $query['t_fax'] = $fax;
          $query['b_fax'] = $fax;
        }else{
          #-------------------------------------------------------------------------
          $query['o_fax'] = '';
          $query['a_fax'] = '';
          $query['t_fax'] = '';
          $query['b_fax'] = '';
        }
        };
        #-----------------------------------------------------------------------------
        $query['ns0'] = $ns1Name;
        $query['ns1'] = $ns2Name;
        #-----------------------------------------------------------------------------
        if($ns3Name)
        $query['ns3'] = $ns3Name;
        #-----------------------------------------------------------------------------
        if($ns4Name)
        $query['ns4'] = $ns4Name;
        #-----------------------------------------------------------------------------
        if($ns1IP && $ns2IP){
        #---------------------------------------------------------------------------
        $query['ns0ip'] = $ns1IP;
        $query['ns1ip'] = $ns2IP;
        }
        #-----------------------------------------------------------------------------
        if($ns3IP)
        $query['ns3ip'] = $ns3IP;
        #-----------------------------------------------------------------------------
        if($ns4IP)
        $query['ns4ip'] = $ns4IP;
        #-----------------------------------------------------------------------------
        $result = Http_Send('/RegTimeSRS.pl',$http,Array(),$query);
        if(Is_Error($result))
        return ERROR | @Trigger_Error('[WebNames_Domain_Register]: не удалось выполнить запрос к серверу');
        #-----------------------------------------------------------------------------
        $result = Trim($result['Body']);
        #-----------------------------------------------------------------------------
        if(Preg_Match('/Success:/',$result))
        return Array('TicketID'=>$domain);
        #-----------------------------------------------------------------------------
        if(Preg_Match('/Error:/',$result))
        return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
        #-----------------------------------------------------------------------------
        return new gException('WRONG_ANSWER',$result);
    }

    /** {@inheritDoc} */
    public function CheckTask($settings, $ticketId) {
        $__args_types = Array('array','string');
        $__args__ = Func_Get_Args();Eval(FUNCTION_INIT);

        if ($ticketId == 'NO') {
          return Array('DomainID'=>0);
        }

        $http = Array(
          'Address'  => $settings['Address'],
          'Port'     => $settings['Port'],
          'Host'     => $settings['Address'],
          'Protocol' => $settings['Protocol'],
          'Charset'  => DFLT_CHARSET
        );

        $query = Array(
          'thisPage'           => 'pispGetApprovalStatus',
          'username'           => $settings['Login'],
          'password'           => $settings['Password'],
          'domain_name'        => $ticketID,
          'interface_revision' => 1,
          'interface_lang'     => 'en'
        );

        $result = Http_Send('/RegTimeSRS.pl', $http, Array(), $query);
        if(Is_Error($result))
          return ERROR | @Trigger_Error('[WebNames_Check_Task]: не удалось выполнить запрос к серверу');
        #-----------------------------------------------------------------------------
        $result = Trim($result['Body']);
        #-----------------------------------------------------------------------------
        if(Preg_Match('/Success:\sDomain\sstatus\sis\s\'([A-Za-z\/]+)\'/', $result, $status)){
          #---------------------------------------------------------------------------
          $status = Next($status);
          #---------------------------------------------------------------------------
          switch($status){
            case 'pending':
              return FALSE;
            case 'approved':
              return Array('DomainID'=>0);
            case 'errsent':
              return new gException('WRONG_CLIENT_DATA','В результате ручной проверки данных клиента регистратором были обнаружены ошибки');
            case 'N/A':
              return Array('DomainID'=>0);
            break;
            default:
              return new gException('WRONG_STATUS','Статус домена ошибочный');
          }
        }
        #-----------------------------------------------------------------------------
        if(Preg_Match('/Error:/',$result))
          return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
        #-----------------------------------------------------------------------------
        return new gException('WRONG_ANSWER',$result);
    }
}
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/Http.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
function WebNames_Domain_Prolong($Settings,$DomainName,$DomainZone,$Years,$ContractID,$DomainID){
  /****************************************************************************/
  $__args_types = Array('array','string','string','integer','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispRenewDomain',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
    'interface_revision' => 1,
    'interface_lang'     => 'en',
    'period'             => $Years
  );
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames_Domain_Prolong]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:/',$Result))
    return Array('TicketID'=>'NO');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
#-------------------------------------------------------------------------------
function WebNames_Domain_Ns_Change($Settings,$DomainName,$DomainZone,$ContractID,$DomainID,$Ns1Name,$Ns1IP,$Ns2Name,$Ns2IP,$Ns3Name,$Ns3IP,$Ns4Name,$Ns4IP){
  /****************************************************************************/
  $__args_types = Array('array','string');
  #-----------------------------------------------------------------------------
  $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);
  /****************************************************************************/
  $Http = Array(
    #---------------------------------------------------------------------------
    'Address'  => $Settings['Address'],
    'Port'     => $Settings['Port'],
    'Host'     => $Settings['Address'],
    'Protocol' => $Settings['Protocol'],
    'Charset'  => 'CP1251'
  );
  #-----------------------------------------------------------------------------
  $Query = Array(
    #---------------------------------------------------------------------------
    'thisPage'           => 'pispRedelegation',
    'username'           => $Settings['Login'],
    'password'           => $Settings['Password'],
    'domain_name'        => SPrintF('%s.%s',$DomainName,$DomainZone),
    'interface_revision' => 1,
    'interface_lang'     => 'en'
  );
  #-----------------------------------------------------------------------------
  $Query['ns0'] = $Ns1Name;
  $Query['ns1'] = $Ns2Name;
  #-----------------------------------------------------------------------------
  if($Ns3Name)
    $Query['ns3'] = $Ns3Name;
  #-----------------------------------------------------------------------------
  if($Ns4Name)
    $Query['ns4'] = $Ns4Name;
  #-----------------------------------------------------------------------------
  if($Ns1IP && $Ns2IP){
    #---------------------------------------------------------------------------
    $Query['ns0ip'] = $Ns1IP;
    $Query['ns1ip'] = $Ns2IP;
  }
  #-----------------------------------------------------------------------------
  if($Ns3IP)
    $Query['ns2ip'] = $Ns3IP;
  #-----------------------------------------------------------------------------
  if($Ns4IP)
    $Query['ns3ip'] = $Ns4IP;
  #-----------------------------------------------------------------------------
  $Result = Http_Send('/RegTimeSRS.pl',$Http,Array(),$Query);
  if(Is_Error($Result))
    return ERROR | @Trigger_Error('[WebNames__Domain_Ns_Change]: не удалось выполнить запрос к серверу');
  #-----------------------------------------------------------------------------
  $Result = Trim($Result['Body']);
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Success:/',$Result))
    return Array('TicketID'=>'NO');
  #-----------------------------------------------------------------------------
  if(Preg_Match('/Error:/',$Result))
    return new gException('REGISTRATOR_ERROR','Регистратор вернул ошибку');
  #-----------------------------------------------------------------------------
  return new gException('WRONG_ANSWER',$Result);
}
?>
