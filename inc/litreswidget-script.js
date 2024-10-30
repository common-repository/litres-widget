jQuery(document).ready(function($) {
    $('.tcode').textillate({
        loop: true,
        minDisplayTime: 5000,
        initialDelay: 800,
        autoStart: true,
        inEffects: [],
        outEffects: [],
        in: {
            effect: 'rollIn',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        out: {
            effect: 'fadeOut',
            delayScale: 1.5,
            delay: 50,
            sync: false,
            shuffle: true,
            reverse: false,
            callback: function() {}
        },
        callback: function() {}
    });
})

function copyCode() {
  var copyText = document.getElementById("copy");
  copyText.select();
  document.execCommand("copy");
  jQuery('.buttoncopy').prop('disabled', true);
  jQuery('.buttoncopy').css('cssText', 'cursor:default!important;');
  jQuery('#tooltip').fadeIn().css('display', 'inline-block').delay(1500).fadeOut(500, function () {
    jQuery('.buttoncopy').prop('disabled', false);
    jQuery('.buttoncopy').css('cssText', 'cursor:pointer!important;');
});
}

