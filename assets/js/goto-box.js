$(document).ready(function(){
    $('#goto_box').focus(function(){
        if(this.value == 'goto') {
            this.value = ''
        }
    })
    $('#goto_box').blur(function(){
        this.value = 'goto'
    })
})