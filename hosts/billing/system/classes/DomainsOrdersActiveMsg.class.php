<?php
/**
 * Created by IntelliJ IDEA.
 * User: vitaly
 * Date: 3/1/12
 * Time: 1:35 AM
 * To change this template use File | Settings | File Templates.
 */
 class DomainsOrdersActiveMsg extends Message {
     public function __construct(array $params) {
         parent::__construct('DomainsOrdersActive');

         $this->setParams($params);
     }
 }