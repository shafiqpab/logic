 <script>
// Design & Develop: Md. Saidul islam Reza
// Cell: 01511100004
var allowed_user_id = [1, 165, 181, 344,374,566,227]; // 


var browserWiseAllow = (function(agent) {
    switch (true) {
        case agent.indexOf("edge") > -1:
            return 160; //"MS Edge";
        case agent.indexOf("edg/") > -1:
            return 160; //"Edge ( chromium based)";
        case agent.indexOf("opr") > -1 && !!window.opr:
            return 160; //"Opera";
        case agent.indexOf("chrome") > -1 && !!window.chrome:
            return 160; //"Chrome";
        case agent.indexOf("trident") > -1:
            return 160; //"MS IE";
        case agent.indexOf("firefox") > -1:
            return 200; //"Mozilla Firefox";
        case agent.indexOf("safari") > -1:
            return 160; //"Safari";
        default:
            return 160; //"other";
    }
})(window.navigator.userAgent.toLowerCase());



(function() {
    'use strict';
    var user_id = <?= $_SESSION['logic_erp']['user_id'];?>;

    var devtools = {
        isOpen: false,
        orientation: undefined
    };

    //const threshold = 160;
    var threshold = 160;
    if ((window.outerHeight - window.innerHeight) < 50) {
        threshold = 5;
    } else {
        threshold = (browserWiseAllow) ? browserWiseAllow : 160;
    }



    var emitEvent = (isOpen, orientation) => {
        window.dispatchEvent(new CustomEvent('devtoolschange', {
            detail: {
                isOpen,
                orientation
            }
        }));
    };


    //alert("Out"+window.outerHeight+"; in"+window.innerHeight);
    let checkInspect = () => {
        var widthThreshold = (window.outerWidth - window.innerWidth) > threshold;
        var heightThreshold = (window.outerHeight - window.innerHeight) > threshold;
        var orientation = widthThreshold ? 'vertical' : 'horizontal';
        if (
            !(heightThreshold && widthThreshold) &&
            ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) ||
                widthThreshold || heightThreshold)
        ) {

            if (!devtools.isOpen || devtools.orientation !== orientation) {
                emitEvent(true, orientation);
            }

            devtools.isOpen = true;
            devtools.orientation = orientation;
        } else {
            if (devtools.isOpen) {
                emitEvent(false, undefined);
            }
            devtools.isOpen = false;
            devtools.orientation = undefined;
        }

    }

    if (typeof module !== 'undefined' && module.exports) {
        module.exports = devtools;
    } else {
        window.devtools = devtools;
    }


    window.devtools.isOpen ? getFirebugUser(<?= $_SESSION['logic_erp']['user_id'];?>) : '';

    window.addEventListener('devtoolschange', event => {
        event.detail.isOpen ? getFirebugUser(<?= $_SESSION['logic_erp']['user_id'];?>) : '';
    });


    function getFirebugUser(user_id) {
        return_global_ajax_value('', 'firebug_user_info', '', 'auto_mail/firebug_user_info');
        if (jQuery.inArray(user_id, allowed_user_id) == -1) {
            alert("Please Close Firebug & We Logout Platform ERP");
            window.location.href = "logout.php";
        }

    }


    window.onresize = function() {
        checkInspect();
    }
    window.onload = function() {
        checkInspect();
    }

})();
 </script>