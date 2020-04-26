var $collectionHolderExperience;
var $addTagButtonExperience = $('<label class="add_tag_link col-lg-12" style="color: #ffffff"><img src="/images/icons8_Plus_50px_3.png" height="56"></img> Ajouter un projet</label>');
var $newLinkLiExperience    = $('#add_tag_link_experience').append($addTagButtonExperience);

var $collectionHolderRoleFirst;
var $addTagButtonRoleFirst = $('<label class="add_tag_link col-lg-12" style="color: #ffffff"><img src="/images/icons8_Plus_50px_3.png" height="56"></img> Ajouter un role</label>');
var $newLinkLiRoleFirst    = $('#add_tag_link_role').append($addTagButtonRoleFirst);
jQuery(document).ready(function() {
    $collectionHolderExperience = $('div.tags');
    $("#last_experience_startDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $("#last_experience_endDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $("#last_experience_projects_0_startDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $("#last_experience_projects_0_finishDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $("#candidate_information_birthDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $collectionHolderExperience.after($newLinkLiExperience);
    $collectionHolderExperience.data('index', $collectionHolderExperience.find(':button').length);
    $("#last_experience_startDate").on('change',function(e){
        if($("#last_experience_endDate").val()  !=  "")
        {
            var formStart   = $("#last_experience_startDate").val().split("/");
            var dateStart   = new Date(formStart[2], formStart[1] - 1, formStart[0]);
            var formEnd     = $("#last_experience_endDate").val().split("/");
            var dateEnd     = new Date(formEnd[2], formEnd[1] - 1, formEnd[0]);
            if(dateStart.getTime() - dateEnd.getTime()>0)
            {
                alert("Date de fin est inférieur à la date de début");
                $("#last_experience_startDate").val(null);
            }
        }
    });
    $("#last_experience_endDate").on('change',function(e){
        var formStart   = $("#last_experience_startDate").val().split("/");
        var dateStart   = new Date(formStart[2], formStart[1] - 1, formStart[0]);
        var formEnd     = $("#last_experience_endDate").val().split("/");
        var dateEnd     = new Date(formEnd[2], formEnd[1] - 1, formEnd[0]);
        if(dateStart.getTime() - dateEnd.getTime()>0)
        {
            alert("Date de fin est inférieur à la date de début");
            $("#last_experience_endDate").val(null);
        }
    });
    $("#last_experience_projects_0_startDate").on('change',function(e){
        var formExperienceStart   = $("#last_experience_startDate").val().split("/");
        var dateExperienceStart   = new Date(formExperienceStart[2], formExperienceStart[1] - 1, formExperienceStart[0]);
        var formExperienceEnd     = $("#last_experience_endDate").val().split("/");
        var dateExperienceEnd     = new Date(formExperienceEnd[2], formExperienceEnd[1] - 1, formExperienceEnd[0]);
        var formProjectStart   = $("#last_experience_projects_0_startDate").val().split("/");
        var dateProjectStart   = new Date(formProjectStart[2], formProjectStart[1] - 1, formProjectStart[0]);
        var formProjectEnd     = $("#last_experience_projects_0_finishDate").val().split("/");
        var dateProjectEnd     = new Date(formProjectEnd[2], formProjectEnd[1] - 1, formProjectEnd[0]);
        if(((dateProjectStart.getTime() - dateExperienceStart.getTime()) < 0) || ((dateProjectStart.getTime() - dateExperienceEnd.getTime()) > 0))
        {
            alert("Il faut que la date de début soit incluse dans la periode de l'experience");
            $("#last_experience_projects_0_startDate").val(null);
        }
        if($("#last_experience_projects_0_finishDate").val()  !=  "")
        {
            if ((dateProjectEnd.getTime() - dateProjectStart.getTime()) <= 0)
            {
                alert("Il faut que la date de fin soit supérieur à la date de début ");
                $("#last_experience_projects_0_startDate").val(null);
            }
        }
    });
    $("#last_experience_projects_0_finishDate").on('change',function(e){
        var formExperienceStart   = $("#last_experience_startDate").val().split("/");
        var dateExperienceStart   = new Date(formExperienceStart[2], formExperienceStart[1] - 1, formExperienceStart[0]);
        var formExperienceEnd     = $("#last_experience_endDate").val().split("/");
        var dateExperienceEnd     = new Date(formExperienceEnd[2], formExperienceEnd[1] - 1, formExperienceEnd[0]);
        var formProjectStart   = $("#last_experience_projects_0_startDate").val().split("/");
        var dateProjectStart   = new Date(formProjectStart[2], formProjectStart[1] - 1, formProjectStart[0]);
        var formProjectEnd     = $("#last_experience_projects_0_finishDate").val().split("/");
        var dateProjectEnd     = new Date(formProjectEnd[2], formProjectEnd[1] - 1, formProjectEnd[0]);
        if(((dateProjectEnd.getTime() - dateExperienceStart.getTime()) < 0) || ((dateProjectEnd.getTime() - dateExperienceEnd.getTime()) > 0))
        {
            alert("Il faut que la date de fin soit incluse dans la periode de l'experience");
            $("#last_experience_projects_0_finishDate").val(null);
        }
        else
        {
            if((dateProjectEnd.getTime() - dateProjectStart.getTime()) <= 0)
            {
                alert("Il faut que la date de fin soit supérieur à la date de début ");
                $("#last_experience_projects_0_finishDate").val(null);
            }
        }
    });
    $collectionHolderRoleFirst = $('div.star');
    $collectionHolderRoleFirst.after($newLinkLiRoleFirst);
    $collectionHolderRoleFirst.data('index', $collectionHolderRoleFirst.find(':input').length);

    $addTagButtonRoleFirst.on('click', function(e) {
        addTagFormRoleFirst($collectionHolderRoleFirst, $newLinkLiRoleFirst);
    });
    $addTagButtonExperience.on('click', function(e) {
        addTagFormExperience($collectionHolderExperience, $newLinkLiExperience);
    });
    if($collectionHolderExperience.find("div.RoleRow").length==0)
    {
        $("#add_tag_link_role>label").click()
    }



});
function addTagFormRoleFirst($collectionHolderRoleFirst, $newLinkLiRoleFirst) {
    var prototype = $collectionHolderRoleFirst.data('prototype');
    var indexRoleFirst = $collectionHolderRoleFirst.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__role__/g, indexRoleFirst);
    $collectionHolderRoleFirst.data('index', indexRoleFirst + 1);
    var $newFormLi = $('<div class="RoleRow"></div>').append(newForm);
    $collectionHolderRoleFirst.append($newFormLi);
    if($collectionHolderExperience.find('div.RoleRow').length>1)
    {
        addTagFormDeleteLink($newFormLi);
    }
}
function addTagFormExperience($collectionHolderExperience, $newLinkLiExpereince) {
    var prototype = $collectionHolderExperience.data('prototype');
    var indexExperience = $collectionHolderExperience.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, indexExperience);
    $collectionHolderExperience.data('index', indexExperience + 1);
    var $newFormLi = $('<div class="experienceRow card"></div>').append(newForm);
    $newFormLi.find('div.card-header').attr('id','heading_'+indexExperience);
    $newFormLi.find('button.btn-link').attr({
        'data-target': '#collapse_'+indexExperience,
        'aria-controls':    'collapse_'+indexExperience
    });
    $newFormLi.find('div.collapse').attr({
        'id': 'collapse_'+indexExperience,
        'aria-labelledby':    'heading_'+indexExperience
    });
    $('div.tags').append($newFormLi);
    $("#last_experience_projects_" + indexExperience + "_technicalSkills").select2();
    $("#last_experience_projects_" + indexExperience + "_startDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true});
    $("#last_experience_projects_" + indexExperience + "_finishDate").datepicker({ dateFormat: 'dd/mm/yy', changeYear: true, changeMonth:true });
    $("#last_experience_projects_" + indexExperience + "_startDate").on('change',function(e){
        if(($("#last_experience_projects_" + indexExperience + "_startDate").val()<$("#last_experience_startDate").val()) || ($("#last_experience_projects_" + indexExperience + "_startDate").val()>=$("#last_experience_endDate").val())){
            alert("Il faut que la date de début soit incluse dans la periode de l'experience");
            $("#last_experience_projects_" + indexExperience + "_startDate").val(null);
        }
        else
        {
            if($("#last_experience_projects_" + indexExperience + "_finishDate").val()  !=  "")
            {
                if (($("#last_experience_projects_" + indexExperience + "_finishDate").val() <= $("#last_experience_projects_" + indexExperience + "_startDate").val()))
                {
                    alert("Il faut que la date de fin soit supérieur à la date de début ");
                    $("#last_experience_projects_0_startDate").val(null);
                }
            }
        }
    });
    $("#last_experience_projects_" + indexExperience + "_finishDate").on('change',function(e){
        if(($("#last_experience_projects_" + indexExperience + "_finishDate").val()<=$("#last_experience_startDate").val()) || ($("#last_experience_projects_" + indexExperience + "_finishDate").val()>$("#last_experience_endDate").val()))
        {
            alert("Il faut que la date de fin soit incluse dans la periode de l'experience");
            $("#last_experience_projects_" + indexExperience + "_finishDate").val(null);
        }
        else
        {
            if(($("#last_experience_projects_" + indexExperience + "_finishDate").val()<=$("#last_experience_projects_" + indexExperience + "_startDate").val()))
            {
                alert("Il faut que la date de fin soit supérieur à la date de début ");
                $("#last_experience_projects_" + indexExperience + "_finishDate").val(null);
            }
        }
    });
    addTagFormDeleteLink($newFormLi);

    var $collectionHolderRole;
    var $addButtonRole    = $('<div id="add_tag_link_'+indexExperience+'_role"></div>');
    var $addTagButtonRole = $('<label class="add_tag_link col-lg-12" style="color: #ffffff"><img src="/images/icons8_Plus_50px_3.png" height="56"></img> Ajouter un role</label>');
    var $newLinkLiRole    = $addButtonRole.append($addTagButtonRole);
    $collectionHolderRole   =  $newFormLi.find('div.star');
    $collectionHolderRole.after($newLinkLiRole);
    $collectionHolderRole.data('index', $collectionHolderRole.find(':button').length);
    $addTagButtonRole.on('click', function(e) {
        addTagFormRole($collectionHolderRole, $newLinkLiRole,$newFormLi);
    });
    if($newFormLi.find('div.RoleRow').length==0)
    {
        $("#add_tag_link_"+indexExperience+"_role>label").click()
    }
}

function addTagFormRole($collectionHolderRole, $newLinkLiRole,$newFormLinn) {
    var prototype = $collectionHolderRole.data('prototype');
    var indexRole = $collectionHolderRole.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__role__/g, indexRole);
    $collectionHolderRole.data('index', indexRole + 1);
    var $newFormLi = $('<div class="RoleRow"></div>').append(newForm);
    $collectionHolderRole.append($newFormLi);
    if($newFormLinn.find('div.RoleRow').length>1)
    {
        addTagFormDeleteLink($newFormLi);
    }
}

function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<div class="actions-td"><span class="flaticon-rubbish-bin"></span></div>');
    $tagFormLi.append($removeFormButton);
    $removeFormButton.on('click', function(e) {
        $tagFormLi.remove();
    });
}



var $collectionHolderOffer;
var $addTagButtonOffer = $('<label class="add_tag_link col-lg-12"><img src="/images/icons8_Plus_50px_3.png" height="56"></img></label>');
var $newLinkLiOffer = $('#add_tag_link').append($addTagButtonOffer);

jQuery(document).ready(function() {
    $collectionHolderOffer = $('table.jobs');
    $collectionHolderOffer.find('tr.jobSkillRow').each(function() {
        addTagFormDeleteLink($(this));
    });
    $collectionHolderOffer.after($newLinkLiOffer);
    $collectionHolderOffer.data('index', $collectionHolderOffer.find(':input').length);
    $addTagButtonOffer.on('click', function(e) {
        addTagFormOffer($collectionHolderOffer, $newLinkLiOffer);
    });
});
function addTagFormOffer($collectionHolderOffer, $newLinkLiOffer) {
    var prototype = $collectionHolderOffer.data('prototype');
    var indexOffer = $collectionHolderOffer.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, indexOffer);
    $collectionHolderOffer.data('index', indexOffer + 1);
    var $newFormLi = $('<tr class="jobSkillRow"></tr>').append(newForm);
    $('table.jobs').append($newFormLi);
    $("#job_offer_jobSkills_" + indexOffer + "_technicalSkill").select2();
    addTagFormDeleteLink($newFormLi);
}
jQuery(document).ready(function(){
    $("#birthDate").on('change',function(e){
        var d               =   new Date();
        var date1_ms        =   $("#candidate_information_birthDate").val().split('/');
        var d2              =   new Date(date1_ms[2],date1_ms[1]-1,date1_ms[0]);
        var diff            =   Math.round((d-d2)/(1000*60*60*24*30*12));
        if(diff <   18)
        {
            alert("Doit être âgé d'au moins 18 ans");
            $("#candidate_information_birthDate").val(null);
        }
    });
});


var $collectionHolderDiploma;
var $addTagButtonDiploma = $('<label class="add_tag_link_diploma col-lg-12" style="color: #ffffff"><img src="/images/icons8_Plus_50px_3.png" height="56"></img> Ajouter un diplôme</label>');
var $newLinkLiDiploma = $('#add_tag_link_diploma').append($addTagButtonDiploma);


var $collectionHolder1;

// setup an "add a tag" link
var $addPlusButton = $('<label class="add_plus_link col-lg-12" style="color: #ffffff"><img src="/images/icons8_Plus_50px_3.png" height="56"></img>Ajouter une certification</label>');
var $newLink = $('#add_plus_link').append($addPlusButton);


jQuery(document).ready(function() {
    $collectionHolderDiploma = $('table.tags');
    $collectionHolderDiploma.find('tr.diplomaRow').each(function() {
        addTagFormDeleteLink($(this));
    });
    $collectionHolderDiploma.after($newLinkLiDiploma);
    $collectionHolderDiploma.data('index', $collectionHolderDiploma.find('tr').length);
    $addTagButtonDiploma.on('click', function(e) {
        // add a new tag form (see next code block)
        addTagFormDiploma($collectionHolderDiploma, $newLinkLiDiploma);
    });

    $collectionHolder1 = $('table.plus');

    // add a delete link to all of the existing tag form li elements
    $collectionHolder1.find('tr.certificationRow').each(function() {
        addTagFormDeleteLink($(this));
    });
    $collectionHolder1.after($newLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder1.data('index', $collectionHolder1.find(':input').length);

    $addPlusButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addPlusForm($collectionHolder1, $newLink);
    });
    if($(".diplomaRow").length==0)
        $("#add_tag_link_diploma>label").click()

    if($(".certificationRow").length==0)
        $("#add_plus_link>label").click()
});
function addTagFormDiploma($collectionHolderDiploma, $newLinkLiDiploma) {
    var prototype = $collectionHolderDiploma.data('prototype');
    var indexDiploma = $collectionHolderDiploma.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, indexDiploma);
    $collectionHolderDiploma.data('index', indexDiploma + 1);
    var $newFormLi = $('<tr class="diplomaRow"></tr>').append(newForm);
    $('table.tags').append($newFormLi);
    $("#diploma_certification_diplomas_" + indexDiploma + "_delivred").datepicker({ dateFormat: 'dd/mm/yy' });
    if($(".diplomaRow").length>1)
    {
        addTagFormDeleteLink($newFormLi);
    }
}
function addPlusForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);
    var technicalSkillInputId   =   "#diploma_certification_certifications_" + index + "_name";
    console.log("#diploma_certification_certifications_" + index + "_name")
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<tr class="certificationRow"></tr>').append(newForm);
    $('table.plus').append($newFormLi);
    $("#diploma_certification_certifications_" + index + "_delivred").datepicker({ dateFormat: 'dd/mm/yy' });
    // add a delete link to the new form
    if($(".certificationRow").length>1)
    {
        addTagFormDeleteLink($newFormLi);
    }
}
function addTagFormDeleteLink($tagFormLi) {
    var $removeFormButton = $('<td class="actions-td"><span class="flaticon-rubbish-bin"></span></td>');
    $tagFormLi.append($removeFormButton);
    $removeFormButton.on('click', function(e) {
        $tagFormLi.remove();
    });
}