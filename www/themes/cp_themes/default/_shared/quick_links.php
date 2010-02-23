<div id="quickLinks">
	<h4>
		<?=lang('quick_links')?>
		<span id="sidebar_quick_links_edit_desc" class="sidebar_hover_desc"><?=lang('click_to_edit')?></span>
	</h4>

	<ul class="bullets">
		<?php foreach($cp_quicklinks as $cp_quicklink):?>
		<li><a href="<?=$this->config->item('base_url').$this->config->item('index_page').'?URL='.$cp_quicklink['link']?>"><?=$cp_quicklink['title']?></a> <a rel="external" href="<?=$this->config->item('base_url').$this->config->item('index_page').'?URL='.$cp_quicklink['link']?>"><img src="<?=$cp_theme_url?>images/external_link.png"/></a></li>
		<?php endforeach;?>
		<li><a href="<?=BASE.AMP.'C=myaccount'.AMP.'M=quicklinks'.AMP.'id='.$this->session->userdata['member_id']?>"><?=lang('quicklinks_manager')?></a></li>
	</ul>
</div> 

<?php
/* End of file quick_links.php */
/* Location: ./themes/cp_themes/default/_shared/quick_links.php */