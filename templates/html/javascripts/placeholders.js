$(document).ready(function(){
  $('input.placeholder').focus(function(){
    if(this.value == 'Go To') {
      this.value = ''
      $(this).removeClass()
    }
  })
  $('input.placeholder').blur(function(){
    if(this.value == '') {
      this.value = 'Go To'
      $(this).addClass('placeholder')
    }
  })
})