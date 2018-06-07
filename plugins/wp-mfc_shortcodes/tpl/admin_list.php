<?php

	//print_r($_FILES);
	if (isset($_FILES['file']) and !$_FILES['file']['error']){
		$list = unserialize(file_get_contents($_FILES['file']['tmp_name']));
		if (!is_array($list))
			echo 'Некорректные данные в файле импорта';
		else
			update_option('tve_shortcode_list', $list);
	}

	$list = get_option('tve_shortcode_list', array());
	if (!is_array($list))
		$list = array();
	
	$content = '';
	if (isset($_POST['nameform'])){
		$_POST = stripslashes_deep($_POST); // удаление экранирующих слешей
		if ($_POST['nameform'] == 'addshortcode'){ // добавление новых данных
			$isset_code = false;
			foreach ($list as $key => $value){
				if ($value['code'] == $_POST['code'])
					$isset_code = true;
			}
			
			if (!$isset_code){
				$list[] = array(
					'group' => $_POST['group'],
					'code' => $_POST['code'],
					'description' => $_POST['description'],
					'text' => $_POST['text'],
					'status' => isset($_POST['status'])? 1 : 0,
					'noHTML' => isset($_POST['noHTML'])? 1 : 0,
				);
				update_option('tve_shortcode_list', $list);
			}else
				echo 'Этот шорткод был использован ранее';
		}
		if ($_POST['nameform'] == 'changeshortcode'){ // обновление данных
			foreach ($list as $key => &$value)
				if ($value['code'] == $_POST['code']){
					$value['group'] = $_POST['group'];
					$value['description'] = $_POST['description'];
					$value['text'] = $_POST['text'];
					$value['status'] = isset($_POST['status'])? 1 : 0;
					$value['noHTML'] = isset($_POST['noHTML'])? 1 : 0;
				}

			update_option('tve_shortcode_list', $list);
		}
	}
	if (isset($_GET['change']))
		foreach ($list as $key => $value){
			if ($value['code'] == $_GET['change']){
				$group = $key;
				$change = $value;
				$content = $value['text'];
			}
		}
	if (isset($_GET['delete'])){
		foreach ($list as $key => $value){
			if ($value['code'] == $_GET['delete'])
				unset($list[$key]);
		}
		update_option('tve_shortcode_list', $list);
	}

	$groups = array();
	foreach ($list as $key => $value) 
		$groups[$value['group']] = 1;

	$editor_id = 'editorshortcode';
	$i = 1;

?>
<div class="tve-shortcode">
	<h3>Редактирование списка шорткодов</h3>
	<?php if (isset($_GET['change'])): ?>
		<div class="block-form">
			<form method="post" action="?page=shortcode">
				<input type="hidden" name="nameform" value="changeshortcode">
				<input type="hidden" name="code" value="<?php echo $_GET['change'] ?>">
				<h3>Редактирование шорткода</h3>
				<p> <input type="text" name="group" placeholder="Группа" value="<?php echo $change['group'] ?>"></p>
				<p> <input type="text" name="description" placeholder="Подсказка" value="<?php echo $change['description'] ?>"></p>
				<p><?php wp_editor( $content, $editor_id, array('textarea_name' => 'text') );?></p>
				<p><label><input type="checkbox" name="status" <?php echo $change['status']? 'checked="checked"':'';?>> &mdash; используется</label>  <label><input type="checkbox" name="noHTML" <?php echo (isset ($change['noHTML']) and $change['noHTML'])? 'checked="checked"':'';?>> &mdash; не использовать разбивку на абзацы</label></p>
				<p><button>Сохранить</button> <a href="?page=shortcode">Отмена</a></p>
			</form>
		</div>
	<?php else: ?>
		<button class="add-new-shortcode">Добавить новый шорткод</button>
		<button class="tve-export"><a href="?page=shortcode&export=1" target="_blank">Экспорт</a></button>
		<button class="tve-import">Импорт</button>
		<div class="block-form" style="display: none;">
			<form method="post">
				<input type="hidden" name="nameform" value="addshortcode">
				<h3>Добавление шорткода</h3>
				<p> <input type="text" name="group" placeholder="Группа"></p>
				<p> <input type="text" name="code" placeholder="Код"></p>
				<p> <input type="text" name="description" placeholder="Подсказка"></p>
				<p><?php wp_editor( $content, $editor_id, array('textarea_name' => 'text') );?></p>
				<p><label><input type="checkbox" name="status" checked="checked"> &mdash; используется</label>  <label><input type="checkbox" name="noHTML"> &mdash; не использовать разбивку на абзацы</label></p>
				<p><button>Сохранить</button></p>
			</form>
		</div>
		<div class="block-import" style="display: none">
			<form method="post" enctype="multipart/form-data">
				<h3>Импорт шорткодов</h3>
				<p>Жду файл: <input type="file" name="file"></p>
				<p><button>Импортировать</button></p>
			</form>
		</div>
		<hr>
		<table class="widefat fixed">
			<tr>
				<th>№</th>
				<th>Код</th>
				<th>Подсказка</th>
				<th>Статус</th>
				<th>Действие</th>
			</tr>
			<?php foreach ($groups as $key => $gr) { $i = 1;?>
				<tr>
					<td colspan="5" class="tve-td-group">Группа: <b><?php echo $key? $key : '__'; ?></b></td>
				</tr>
				<?php foreach ($list as $value) {?>
					<?php if ($value['group'] == $key): ?>
						<tr>
							<td><?php echo $i++ ?></td>
							<td><a href="?page=shortcode&change=<?php echo $value['code'] ?>" title="Редактировать код"><?php echo $value['code'] ?></a></td>
							<td><?php echo $value['description'] ?></td>
							<td><?php echo $value['status']? 'используется' : 'остановлен' ?></td>
							<td><a href="?page=shortcode&delete=<?php echo $value['code'] ?>">Удалить</a></td>
						</tr>
					<?php endif; ?>
				<?php }?>
			<?php } ?>
		</table>

	<?php endif; ?>
</div>