<?php
Trait ClassDescriptor
{
	public function _getProperties()
	{
		return get_object_vars($this);
	}
}
?>
