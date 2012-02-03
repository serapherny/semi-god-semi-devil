<?php if (isset($class)) {?><div class="<?php echo $class;?>"><?php }?>

<?php if (is_array($content)){
        foreach($content as $sub_content) {?> 
          <?php if (isset($sub_class)) {?><div class="<?php echo $sub_class;?>"><?php }?>
            <?php echo $sub_content;?>
          <?php if (isset($sub_class)) {?></div><?php }?>
<?php   }
      } else {
        echo $content;
      }?>

<?php if (isset($class)) {?></div><?php }?>