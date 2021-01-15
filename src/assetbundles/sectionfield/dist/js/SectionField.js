const idAllowed = $(document).find(`[data-ww="selectAll"]`).find("label");

$(idAllowed).click(function () {
    let checked = $(this).parent().find(".checkbox").is(":checked");
    
    let idSelectAllInput = $(this).parent().parent().parent().find(`[data-ww="allowedSections"]`);

    let excludedSections = $(this).parent().parent().parent().find(`[data-ww="excludedSections"]`);

    if (checked === true) {
        $(idSelectAllInput).find(".checkbox").each(function (i, e) {
            $(e).prop('checked', false);
            $(e).css("pointer-events", "unset");
        });

        $(excludedSections).css("display", "none");

        $(idSelectAllInput).find("label").each(function (i, e) {
            $(e).css("opacity", "1");
            $(e).css("pointer-events", "unset");
        });

    } else {
        $(idSelectAllInput).find(".checkbox").each(function (i, e) {
            $(e).prop('checked', true);
            $(e).css("pointer-events", "none");
        });

        $(excludedSections).css("display", "block");

        $(idSelectAllInput).find("label").each(function (i, e) {
            $(e).css("opacity", "0.5");
            $(e).css("pointer-events", "none");
        });
    }
});



function checkSelectAll() {
    let c = $(document).find(`[data-ww="selectAll"]`);

    c.each(function () {
        let checked;

        let checkType = $(this).parent().parent().parent().parent().parent().parent().parent().parent().attr('class')

        let  idSelectAllInput = $(this).parent().parent().parent().find(`[data-ww="allowedSections"]`);

        if (checkType === "input ltr") {
            checked = $(this).parent().find(".checkbox").is(":checked");
        } else {
            checked = $(this).find(".checkbox").is(":checked");
        }

        let excludedSections = $(this).parent().parent().parent().find(`[data-ww="excludedSections"]`);

        if (checked === false) {
            $(idSelectAllInput).find(".checkbox").each(function (i, e) {
                $(e).prop('checked', false);
                $(e).css("pointer-events", "unset");
            });

            $(excludedSections).css("display", "none");

            $(idSelectAllInput).find("label").each(function (i, e) {
                $(e).css("opacity", "1");
                $(e).css("pointer-events", "unset");
            });

        } else {
            $(idSelectAllInput).find(".checkbox").each(function (i, e) {
                $(e).prop('checked', true);
                $(e).css("pointer-events", "none");
            });

            $(excludedSections).css("display", "block");

            $(idSelectAllInput).find("label").each(function (i, e) {
                $(e).css("opacity", "0.5");
                $(e).css("pointer-events", "none");
            });
        }
    })
}

checkSelectAll()
