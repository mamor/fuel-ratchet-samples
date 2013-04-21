<div class="row">

	<div class="span2">

		<div id="members">
			<ul></ul>
		</div><!-- /#members-->

		<?php echo Html::anchor('ratchet/ws/chat/logout', 'Logout', array('class' => 'btn btn-link')); ?>

	</div><!-- /.span-->

	<div class="span10">

		<div id="chat">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width:25%;">From</th>
						<th style="width:50%;">Message</th>
						<th style="width:25%;">At</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div><!-- /#chat-->

		<div id="message">
			<input type="text" maxlength="20" class="input-xxlarge" placeholder="Your message ..." />

			<div class="alert alert-error">
				<button class="close">×</button>
				<p></p>
			</div>
		</div><!-- /#message-->

	</div><!-- /.span-->

</div><!-- /.row-->

<script>
$(document).ready(function() {

	
	/**
	 * エラーアラートの非表示
	 */
	$('#message > .alert-error > .close').click(function() {
		$(this).parent().hide();
	});

	/**
	 * WebSocketのコネクション
	 */
	var ws = new WebSocket("<?php echo Ratchet::get_uri('Ratchet_Ws_Chat'); ?>");

	/**
	 * 接続
	 */
	ws.onopen = function(e) {
		// do nothing.
	};

	/**
	 * 各種受信
	 */
	var my_resource_id;
	ws.onmessage = function(e) {

		var json = $.parseJSON(e.data);

		switch (json.type) {

			// メンバー一覧受信
			case 'open':
				my_resource_id = json.resource_id;

				$.each(json.members, function(index, member) {
					var li = $('<li></li>');
					li.attr('id', member.resource_id);

					var span = $('<span>' + member.username + '</span>');

					if (member.resource_id == my_resource_id) {
						span.attr('class', 'me');
						li.append(span);

						$('#members > ul').prepend(li);
					} else {
						li.append(span);

						$('#members > ul').append(li);
					}

				});
			break;

			// 入室者受信
			case 'join':
				var li = $('<li></li>');
				li.attr('id', json.resource_id);

				var span = $('<span>' + json.username + '</span>');

				li.append(span);

				li.appendTo($('#members > ul')).hide().fadeIn(1000);
			break;

			// 退室者受信
			case 'leave':
				$('#' + json.resource_id).fadeOut(1000, function() {
					$(this).remove();
				});
			break;

			// メッセージ受信
			case 'msg':
				var chat = $('#chat > table > tbody > tr');
				var max = 10;
				if (max <= chat.length) {
					$(chat).each(function() {
						$(this).remove();
						if($('#chat > table > tbody > tr').length < max) {
							return false;
						}
					});
				}

				var tr = $('<tr></tr>');

				if (json.resource_id == my_resource_id) {
					tr.attr('class', 'info');
				}

				var td_username = $('<td>' + json.username + '</td>');
				var td_msg = $('<td>' + json.msg + '</td>');
				var td_posted_at = $('<td>' + new Date(json.posted_at * 1000).toLocaleString() + '</td>');
				tr.append(td_username).append(td_msg).append(td_posted_at);
				tr.appendTo($('#chat > table > tbody')).hide().fadeIn(1000);
			break;

			// エラー
			case 'error':
				var p = $('#message > .alert-error > p');
				p.text('');
				$.each(json.errors, function(k, v) {
					p.text(p.text() + "\n" + v);
				});
				$('#message > .alert-error').show();
			break;
		}

	};

	/**
	 * メッセージ送信
	 */
	$("#message > input").keypress(function(event) {
		if(event.which === 13 && $(this).val()) {
			ws.send($(this).val());
			$(this).val('');
		}
	});

});
</script>
