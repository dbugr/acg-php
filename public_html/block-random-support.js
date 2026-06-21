// document.domain='yourdomain.com';  // Uncomment and edit if Access is Denied runtime JavaScript error occurs

function iFrameHeight(obj) {
  aID = obj.id;

  // if contentDocument exists, W3C compliant (Mozilla)
  if (document.getElementById(aID).contentDocument){
     obj.height = document.getElementById(aID).contentDocument.body.scrollHeight;
  } else {
   // IE
     obj.style.height = document.frames[aID].document.body.scrollHeight;
  }
}
