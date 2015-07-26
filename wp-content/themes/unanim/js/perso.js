/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function(){
    jQuery("#debug").children().hide();
    jQuery("#debug").click(function(){
            jQuery(this).children().toggle(400);
            
          });
});

