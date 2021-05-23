$(document).ready(function (e) {
    
    $(".popup").popover({ trigger: "hover" , container: 'body'});
    
    var key = "dashboard:target:settled";
    var item = storage.getItem(key);
    var oldSettled = null;
    if(item != null && typeof item.settled != "undefined") {
        oldSettled = item.settled;
    }
    if(oldSettled != null) {
        $("input[name='settled']").val(oldSettled);
        reCalTarget(oldSettled);
    }
    
    function reversePercentage(startVal, percentage) {
        return (100 / percentage) * startVal;
    }
    
    function reCalTarget(target) {
        target = parseFloat(target);
        var conversion = 0;

        conversion = $(".conversion-unconditional").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-unconditional").text(numeral(target).format('0,0'));

        conversion = $(".conversion-pending").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-pending").text(numeral(target).format('0,0'));

        conversion = $(".conversion-submitted").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-submitted").text(numeral(target).format('0,0'));

        avg_submitted = $(".conversion-submitted").attr('data-avg');
        target = target / avg_submitted;
        conversion = $(".conversion-appts").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-appts").text(numeral(target).format('0,0'));

        conversion = $(".conversion-calls").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-calls").text(numeral(target).format('0,0'));

        conversion = $(".conversion-leads").attr('data-conversion');
        conversion = parseFloat(conversion);
        target = reversePercentage(target, conversion);
        $(".target-leads").text(numeral(target).format('0,0'));
    }
    $("input[name='settled']").keyup(function(e) {

        reCalTarget($(this).val());
        storage.save(key, {"settled": $(this).val()});
    });
});