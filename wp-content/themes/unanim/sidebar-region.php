<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="sidebar" role="complementary">
   <?php 
   echo set_regions_list_menu(array_shift(get_the_region())->slug);
   ?>
</div>

