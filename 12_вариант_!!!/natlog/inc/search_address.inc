<div class="kdo-main">
	<h1 style='text-align:center;'>Поиск организации</h1>
	<form class='input-search-address' onsubmit='searchAddress(this); return false;'>
		<input type='text' placeholder='Введите адрес или название организации'><br>
		<label><input type='radio' name='search_type' value='address' checked onchange='goSearchAddress();'> по адресу</label>&nbsp;&nbsp;&nbsp;<label><input type='radio' name='search_type' value='name' onchange='goSearchAddress();'> по названию</label>
	</form>
	<br>
	<div id='form-answer'></div>
</div>

<?php



?>