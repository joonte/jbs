<?php
/**
 *
 *  Joonte Billing System
 *
 *  Copyright © 2012 Vitaly Velikodnyy
 *
 */
class gException {
    
    /* Exception code */
    public $CodeID = '';

    /* Description */
    public $String = '';

    /* Parent exception */
    public $Parent = NULL;

    /**
     * Constructor.
     *
     * @param <type> $CodeID
     * @param <type> $String
     * @param <type> $Parent
     * @return <type>
     */
    function __construct($CodeID,$String,$Parent = NULL){
      $__args_types = Array('string','string','NULL,object');
      $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

      Debug(SPrintF('[gException]: [%s]=(%s)',$CodeID,$String));

      $this->CodeID = $CodeID;
      $this->String = $String;
      $this->Parent = $Parent;

      return $this;
    }
}
?>
