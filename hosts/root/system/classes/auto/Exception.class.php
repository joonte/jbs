<?php
/**
 * @author Великодный В.В. (Joonte Ltd.)
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

      Debug(SPrintF('[Exception]: [%s]=(%s)',$CodeID,$String));

      $this->CodeID = $CodeID;
      $this->String = $String;
      $this->Parent = $Parent;

      return $this;
    }
}
?>