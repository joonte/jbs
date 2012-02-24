//------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
//------------------------------------------------------------------------------
function selectStars(e, $ticketId, $starId) {
  for(var $i = 0; $i < 9; $i++) {
    document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/DisableStar.png';
  }

  for(var $i = 0; $i <= $starId; $i++) {
    document.getElementById('star_'+$ticketId+'_'+$i).src = '/styles/billing/Images/Icons/EnableStar.png';
  }
}

