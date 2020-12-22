const idAllowed = '#types-wmd-sectionandproducttype-fields-SectionField-allowedSections-field';
const idExclude = '#types-wmd-sectionandproducttype-fields-SectionField-excludedSections-field';
const idSelectAllInput = '#types-wmd-sectionandproducttype-fields-SectionField-selectAll'
let countCheckedAllow = $(`${idAllowed} input:checkbox:checked`).length;
let countAllAllow = $(`${idAllowed} input:checkbox`).length;


//check initial status select all
if (countCheckedAllow === countAllAllow) {
    $(`${idSelectAllInput}`).prop('checked', true);
} else {
    $(`${idSelectAllInput}`).prop('checked', false);
}

//change chackboxes allow and exclude after click on select all
$(`${idSelectAllInput}`).click(function(){
    if ($(this).is(':checked')){
        $(`${idAllowed} input:checkbox`).prop('checked', true);
        $(`${idExclude} input:checkbox`).prop('checked', false);
    } else {
        $(`${idAllowed} input:checkbox`).prop('checked', false);
        $(`${idExclude} input:checkbox`).prop('checked', true);
    }
});

//
$(`${idAllowed} input:checkbox`).click(function(){

    if ($(this).is(':checked')){
        let countCheckedAllow = $(`${idAllowed} input:checkbox:checked`).length;
        if (countCheckedAllow === countAllAllow) {
            $(`${idSelectAllInput}`).prop('checked', true);
        } else {
            $(`${idSelectAllInput}`).prop('checked', false);
        }
    } else {
        let countCheckedAllow = $(`${idAllowed} input:checkbox:checked`).length;
        if (countCheckedAllow === countAllAllow) {
            $(`${idSelectAllInput}`).prop('checked', true);
        } else {
            $(`${idSelectAllInput}`).prop('checked', false);
        }
    }
});

$(`${idExclude} input:checkbox`).click(function(){
    let countCheckedAllow = $(`${idExclude} input:checkbox:checked`).length;
    if (countCheckedAllow > 0) {
        $(`${idSelectAllInput}`).prop('checked', false);
    } else {
        $(`${idSelectAllInput}`).prop('checked', true);
    }
});

$('input:checkbox').click(function(){

    let name = $(this).parent().find("label").text();

    if ($(this).is(':checked')){
        $('#settings').find("fieldset").each((i, e)=>{
            $(e).find('div').each((i, e) => {
                $(e).find('label').each((i,e) => {
                    if ($(e).text() === name) {
                        $(e).parent().find('input').prop('checked', false);
                        $(this).parent().find('input').prop('checked', true);
                    }
                })
            })
        })
    } else {
        $('#settings').find("fieldset").each((i, e)=>{
            $(e).find('div').each((i, e) => {
                $(e).find('label').each((i,e) => {
                    if ($(e).text() === name) {
                        $(e).parent().find('input').prop('checked', true);
                        $(this).parent().find('input').prop('checked', false);
                    }
                })
            })
        })
    }
});



$('#settings').find("fieldset").each((i, e)=>{
    $(e).find('div').each((i, e) => {
        let check = $(e).find('.checkbox').is(':checked');
        let name = $(e).find('label').text().replace(/ /g,'').replace(/(\r\n|\n|\r)/gm, "");
        $(e).find('.checkbox').attr('data-check', check);
        $(e).find('.checkbox').attr('data-name', name);
    })
});


$(".checkbox").each((i, e)=>{
    let name = $(e).attr("data-name");
    let c = $("#settings").find("input[data-name='" + name + "']");

    if ($(c[0]).attr("data-check") === $(c[1]).attr("data-check") && $(c[0]).attr("data-check") === "true") {
        $(c[1]).prop('checked', false)
    } else if ($(c[0]).attr("data-check") === $(c[1]).attr("data-check") && $(c[0]).attr("data-check") === "false") {
        $(c[1]).prop('checked', true)
    }
});