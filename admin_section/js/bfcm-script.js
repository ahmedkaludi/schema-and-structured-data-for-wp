jQuery(document).ready(function($) {
    
    // 1. Get Data from PHP
    var targetDate = bfcmData.targetDate;
    var offerLink  = bfcmData.offerLink;

    // 2. HTML Structure
    var bannerHTML = `
        <div class="bfcm-banner-header" style="display:flex;">
            <div class="bfcm-timer">
                <div class="timer-box"><span id="d">00</span><label>Days</label></div>
                <div class="timer-box"><span id="h">00</span><label>Hours</label></div>
                <div class="timer-box"><span id="m">00</span><label>Mins</label></div>
                <div class="timer-box"><span id="s">00</span><label>Secs</label></div>
            </div>
            <div class="bfcm-content">
                <h2>Black Friday / Cyber Monday / Christmas</h2>
                <p>Sale is here! 50% off on Everything for a very limited time!!!</p>
            </div>
            <div>
                <a href="${offerLink}" target="_blank" class="bfcm-btn">Claim Offer</a>
            </div>
        </div>
    `;

    // 3. Inject Banner
    if($('.wrap > h1').length) { 
        $('.wrap > h1').after(bannerHTML); 
    } else { 
        $('.wrap').prepend(bannerHTML); 
    }

    // 4. Countdown Timer Logic
    var countDownDate = new Date(targetDate).getTime();
    
    var timerInterval = setInterval(function() {
        var now = new Date().getTime();
        var distance = countDownDate - now;

        // Auto Hide logic (Client Side)
        if (distance < 0) {
            clearInterval(timerInterval);
            $('.bfcm-banner-header').fadeOut(); 
            return;
        }

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        $('.bfcm-banner-header #d').text(days < 10 ? '0'+days : days);
        $('.bfcm-banner-header #h').text(hours < 10 ? '0'+hours : hours);
        $('.bfcm-banner-header #m').text(minutes < 10 ? '0'+minutes : minutes);
        $('.bfcm-banner-header #s').text(seconds < 10 ? '0'+seconds : seconds);
    }, 1000);
});