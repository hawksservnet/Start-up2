<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>
<?= $this->Html->css('jquery-ui')?>
<style>
   .border-bold {
       border-bottom:4px solid ;
    }
</style>
<div class="the-title">
	<!-- <h4>シフト一覧</h4> -->
	<h2 class="title-01">シフト一覧</h2>
</div>
	<div class="show-time">
		<?= $this->Flash->render() ?>
		<div class="clearfix"></div>
		<ul>
			<li class="fm-left">
				<a href="#!" id="left"><< 前週へ</a>
			</li>
			<li>
				<span name="week_range"></span>
			</li>
			<li class="fm-right">
				<a href="#!" id="right">次週へ >></a>
			</li>
		</ul>
	</div>

<?= $this->Form->create(null,['class'=>'','id'=>'shiftfrm','method'=>'post','url'=>'']) ?>
	<div id="finnal-tabs">
		<table class="finnal-tabs ">
			<tr>
				<th class="border-white"></th>
				<?php
					echo "<th class='border-white'>".date("m/d",strtotime($initsdate))."(月曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+1 days', strtotime($initsdate)))."(火曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+2 days', strtotime($initsdate)))."(水曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+3 days', strtotime($initsdate)))."(木曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+4 days', strtotime($initsdate)))."(金曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+5 days', strtotime($initsdate)))."(土曜日）</th>";
					echo "<th class='border-white'>".date('m/d', strtotime('+6 days', strtotime($initsdate)))."(日曜日）</th>";
				?>
			</tr>

			<?php
            $k=1;

				for($hour=0; $hour<11; $hour++){
					for($order=0; $order<3; $order++,$k++){
                        if($k%3==0) $_class ='class="border-bold"';
                        else $_class='';
						echo '<tr >';
						if($order==0){
							echo 	'<td class="bg-caret"  rowspan="3">'.('10'+$hour).':00</td>';
						}
						for($day=0; $day<7; $day++){
							$name = date('Y-m-d', strtotime('+'.$day.' days', strtotime($initsdate)))
								.'_'.('10'+$hour);
							$date = date('Y-m-d', strtotime('+'.$day.' days', strtotime($initsdate)));
                        	$time = ('10'+$hour) . '00';

							if(isset($dataRender[$date][$time][$order]['cid'])) {
								$valueCID = $dataRender[$date][$time][$order]['cid'];
								$valueStat = $dataRender[$date][$time][$order]['status'];
								$valueSID = $dataRender[$date][$time][$order]['sid'];
							} else {
								$valueCID = 0;
								$valueStat = '';
								$valueSID = 0;
							}
							// echo $valueID.'~'.$valueStat.'<br>';

							$shiftname = $name;
							$cname = 'change_'.$name;
							$dname = 'date_'.$name;
							$tname = 'time_'.$name;
							$sname = 'sid_'.$name;

							echo '<td '.$_class.'>'.$this->Form->input($shiftname.'[]', array('type'=>'select',
								'data-status' =>$valueStat,
								'data-cid' =>$valueCID,
								'class'=>'shift',
								'id'=>$valueSID,
								'value' => $valueCID,
								'label'=>'', 'options'=>$arrConcier, 'default'=>'0')).'</td>'; 		  // cid
							echo $this->Form->hidden($shiftname.'_Arr[]', array('type'=>'hidden',
								'id'=> 'IDHid_'.$valueSID,
								// 'data-id' => $valueSID,
								'class'=>'',
								'value' => $valueSID.'_1')); // 1 edit 2 del
						}
						echo '</tr>';
					}
				}
			?>

		</table>
	</div>
	<div class="finnal-submit">
		<button type="button" id="save">保存</button>
	</div>

<?= $this->Form->input('changeArr', array('type'=>'hidden','id'=>'changeArr','class'=>'')); ?>
<?= $this->Form->input('delArr', array('type'=>'hidden','id'=>'delArr','class'=>'')); ?>
<?= $this->Form->end() ?>

<div id="dialog-confirm" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        申し込みユーザがいます。変更をユーザに連絡しますか？
    </p>
</div>

<div id="dialog-dup" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        すでに同一日時に登録済のコンシェルジュです。
    </p>
</div>

<div id="dialog-undel" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        すでに申し込みがあるため、枠の削除ができませんでした。
    </p>
</div>

<div id="dialog-save" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        シフト情報を保存します。よろしいですか？
    </p>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		// $('.shift').val('0');

		var mondate = '<?= $initsdate ?>';
		var sundate = '<?= $initedate ?>';
		$('span[name="week_range"]').text(mondate+'～'+sundate);
		var startdate = new Date(mondate);
		var url = '<?= $this->Url->build(['controller' => 'shift', 'action' => 'index'])?>' + '/';
		startdate.setDate(startdate.getDate() - 7);
		document.getElementById("left").setAttribute("href", url + startdate.getFullYear() + '-' + (startdate.getMonth() + 1) + '-' + startdate.getDate());
		startdate.setDate(startdate.getDate() + 14);
		document.getElementById("right").setAttribute("href", url + startdate.getFullYear() + '-' + (startdate.getMonth() + 1) + '-' + startdate.getDate());

		$(".shift").change(function() {
			var id = $(this).attr("id").substring(5); // Begin with _day_1_order_1_hour_10

			var nameArr = $(this).attr("name");
			var oldC = $(this).attr("data-cid");
			var valArr=[];
			$('select[name="'+nameArr+'"] option:selected').each(function(i, sel) {
			  	valArr.push($(sel).val());
			});
			fre = 0;
			for (var i = 0; i < valArr.length; i++) {
				if ($(this).val() == valArr[i]) {
					fre++;
				}
			}
			if (fre > 1 && $(this).val() != '0') {
				$(this).val(oldC);
				$("#dialog-dup").dialog({
	                resizable: false,
	                height: "auto",
	                width: 400,
	                modal: true,
	                buttons: {
	                    "OK": function () {
	                        $(this).dialog("close");

	                    },
	                },
	                closeOnEscape: false,
	                open: function (event, ui) {
	                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
	                }
	            });
			} else {
				if ($(this).val() == '0' && $(this).attr("data-status") == '1') {
					// alert('すでに申し込みがあるため、枠の削除ができませんでした');
				    $(this).val($(this).attr("data-cid")); //set back
				    $("#dialog-undel").dialog({
		                resizable: false,
		                height: "auto",
		                width: 400,
		                modal: true,
		                buttons: {
		                    "OK": function () {
		                        $(this).dialog("close");
		                    },
		                },
		                closeOnEscape: false,
		                open: function (event, ui) {
		                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
		                }
		            });
				    return;                  			  //abort!
				}
				else if ($(this).val() == '0' && $(this).attr("data-status") == '0') {
				    document.getElementById($(this).attr("id")).setAttribute("data-change", 2);
				    var hidvar = $(this).attr('id');
				    if (hidvar != '') {
				    	$('#IDHid_'+hidvar).val($(this).attr('id')+"_2");
				    }
                    $('#save').click();
				}
				else if ($(this).val() > '0' && $(this).attr("data-cid") != $(this).val() && $(this).attr("data-cid") != '0' && $(this).attr("data-status") != '0') {
				    document.getElementById($(this).attr("id")).setAttribute("data-change", 1);
				    var this_ = $(this);
				    $("#dialog-confirm").dialog({
		                resizable: false,
		                height: "auto",
		                width: 400,
		                modal: true,
		                buttons: {
		                    "OK": function () {
		                        $('#change'+id).val('1');
		                        var hidvar = this_.attr('id');
							    if (hidvar != '') {
							    	$('#IDHid_'+hidvar).val(this_.attr('id')+"_1");
							    }
		                        $(this).dialog("close");
                                $('#save').click();
		                    },
		                    "キャンセル": function () {
		                    	this_.val(this_.attr("data-cid"));
		                        $(this).dialog("close");
		                    }
		                },
		                closeOnEscape: false,
		                open: function (event, ui) {
		                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
		                }
		            });
				}
				else
                {
                    $('#save').click();
                }
			}
		});

		$('#save').click(function () {
			$("#dialog-save").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $('#shiftfrm').submit();
                    },
                    "キャンセル": function () {
                        $(this).dialog("close");
                    }
                },
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
                }
            });
		});

	});
</script>
