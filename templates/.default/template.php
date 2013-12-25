<? //echo '<pre>'; var_dump($arParams); echo '</pre>'; ?>

<link href="/bitrix/cache/css/s1/bitrix24/page_97a9cf0a94485f662b5ef7b83ed33beb/page_97a9cf0a94485f662b5ef7b83ed33beb_058605394fbddc68e2d7c658ce22e9b9.css?138660157241499" type="text/css" rel="stylesheet">

<div class="tasks-top-menu-wrap">
	<div class="tasks-top-menu" id="task-menu-block">
		<?foreach($arParams["years"] as $year):?>
			<span class="tasks-top-item-wrap <?if($year==$arParams["year_select"]):?>tasks-top-item-wrap-active<?endif;?>">
				<a class="tasks-top-item" href="?year=<?=$year?>">
					<span class="tasks-top-item-text"><?=$year?></span>
				</a>
			</span>
		<?endforeach;?>
	</div>
</div>

<div class="calendar">
<?foreach($arResult as $year => $arYear):?>
	<div class="year"><h2><?=$year?></h2></div>
	<?foreach($arYear as $month => $arWeeks):?>
		<div class="month">
			<div class="month_name"><?=$arParams["month"][$month]?>
				<span class="month_num">'
					<?if($month<10):?>0<?endif;?><?=$month?>
				</span>
			</div>

			<table class="month_data">
				<tr class="num_in_week">
					<th></th>
					<?foreach($arParams["num_in_week"] as $name_in_week):?>
						<th><?=$name_in_week?></th>
					<?endforeach;?>
				</tr>
				<?foreach($arWeeks as $num_week => $arDays):?>
					<tr>
						<td class="num_week"><?=$num_week?></td>
						<?for($num_in_week=1; $num_in_week<=7; $num_in_week++):?>
							<td class="<?=$arDays[$num_in_week]['type_code']?>
								<?if($arParams["today"]["year"]==$year &&
									 $arParams["today"]["month"]==$month &&
									 $arParams["today"]["day"]==$arDays[$num_in_week]['value']):?>
									today
								<?endif;?>
							" title="<?=$arDays[$num_in_week]['title']?>">
								<?=$arDays[$num_in_week]['value']?>
							</td>
						<?endfor;?>
					</tr>
				<?endforeach;?>
			</table>
		</div>
	<?endforeach;?>
<?endforeach;?>
</div>
<br><br>
<div class="legend">
	<div class="legend_item"><span class="legend_square today">1</span> — Текущий день</div>
	<div class="legend_item"><span class="legend_square work_day">2</span> — Рабочий день</div>
	<div class="legend_item"><span class="legend_square short_day">3</span> — Короткий день</div>
	<div class="legend_item"><span class="legend_square week_end">4</span> — Выходной или праздничный день</div>
</div>

<?/*<script type="text/javascript" src="<?=$templateFolder?>/script.js"></script>*/?>