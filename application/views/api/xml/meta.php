<?='<?xml version="1.0" encoding="utf-8"?>'?>
<root>
	<? foreach(array_flip(states_array()) as $short => $long): ?>
	<state id="<?=$short?>"><![CDATA[<?=$long?>]]></state>
	<? endforeach ?>
	<? foreach($actions as $action): ?>
	<action id="<?=$action->id?>"><![CDATA[<?=$action->title?>]]></action>
	<? endforeach ?>
</root>