/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function(){
    jQuery('#categorychecklist li input[type="checkbox"]').click(function(){
        jQuery('#categorychecklist li input[type="checkbox"]').each(function(){
            jQuery('#categorychecklist li input[type="checkbox"]').attr('checked',false);
        });
        jQuery(this).attr('checked',true);
    });
    jQuery('tr.inline-edit-row ul.cat-checklist li input[type="checkbox"]').click(function(){
        jQuery('tr.inline-edit-row ul.cat-checklist li input[type="checkbox"]').each(function(){
            jQuery('tr.inline-edit-row ul.cat-checklist li input[type="checkbox"]').attr('checked',false);
        });
        jQuery(this).attr('checked',true);
    });
});

