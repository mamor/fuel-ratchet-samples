<!--配信 (WampServerのonPublishメソッドが呼ばれる)-->
<div id="publish">
<select>
	<option value="topic_1">Topic 1</option>
	<option value="topic_2">Topic 2</option>
	<option value="topic_3">Topic 3</option>
	<option value="invalid_topic">Invalid Topic</option>
</select>
<input type="text" maxlength="20" />
<button class="btn">Publish</button>
</div>

<hr />

<!--購読 (WampServerのonSubscribeメソッドが呼ばれる)-->
<div id="subscribe">
<select>
	<option value="topic_1">Topic 1</option>
	<option value="topic_2">Topic 2</option>
	<option value="topic_3">Topic 3</option>
	<option value="invalid_topic">Invalid Topic</option>
</select>
<button class="btn">Subscribe</button>
</div>

<hr />

<!--購読解除 (WampServerのonUnSubscribeメソッドが呼ばれる)-->
<div id="unsubscribe">
<select>
	<option value="topic_1">Topic 1</option>
	<option value="topic_2">Topic 2</option>
	<option value="topic_3">Topic 3</option>
	<option value="invalid_topic">Invalid Topic</option>
</select>
<button class="btn">Unsubscribe</button>
</div>

<hr />

<!--RPC (WampServerのonCallメソッドが呼ばれる)-->
<div id="rpc">
<select>
	<option value="get_subscribing_topics">Get Subscribing Topics</option>
	<option value="invalid_method">Invalid Method</option>
</select>
<button class="btn">Call</button>
</div>

<hr />

<p>Check your console.</p>

<script>
$(document).ready(function() {
	var sess; // WampServerとのコネクション
	var wsuri = '<?php echo Ratchet::get_uri('Ratchet_Wamp_Api'); ?>';
	connect();

	function connect() {
		/**
		 * 使い方等: http://autobahn.ws/js
		 */
		sess = new ab.Session(wsuri,

			// コネクション接続時のコールバック関数
			function() {
				console.log("Connected!");

				// 各種エラーメッセージ受信用
				sess.subscribe('error', function (topic, event) {
					console.log("-- Error received --");
					console.log("Topic: " + topic);
					console.log("event: " + JSON.stringify(event));
				});
			},

			// コネクション切断時のコールバック関数
			// TODO: 以下の各ケースの発生タイミングを確認
			function(reason) {
				switch (reason) {
					case ab.CONNECTION_CLOSED:
						// 意図した切断の場合?
						console.log("Connection was closed properly - done.");
					break;
					case ab.CONNECTION_UNREACHABLE:
						// WampServerに到達できなかった場合?
						console.log("Connection could not be established.");
					break;
					case ab.CONNECTION_UNSUPPORTED:
						// ブラウザがWebSocketをサポートしていない場合
						console.log("Browser does not support WebSocket.");
					break;
					case ab.CONNECTION_LOST:
						// 意図しない切断の場合?
						console.log("Connection lost - reconnecting ...");

						// 1秒後に再接続を試みる
						window.setTimeout(connect, 1000);
					break;
				}
			}
		);
	};

	// 配信 (WampServerのonPublishメソッドが呼ばれる)
	$("#publish > button").click(function() {
		var input = $("#publish > input");
		var select = $("#publish > select");

		console.log("-- Publish --");
		if(input.val().length == 0) {
			console.log("Input is empty.");
		} else {
			console.log("Topic: " + select.val());
			console.log("Msg: " + input.val());

			sess.publish(select.val(), JSON.stringify({msg: input.val()}));
			input.val('');
		}
	});

	// 購読 (WampServerのonSubscribeメソッドが呼ばれる)
	$("#subscribe > button").click(function() {
		var select = $("#subscribe > select");

		console.log("-- Subscribe --");
		console.log("Topic: " + select.val());

		sess.subscribe(select.val(), function (topic, event) {
			console.log("-- Received --");
			console.log("Topic: " + topic);
			console.log("event: " + event);
		});
	});

	// 購読解除 (WampServerのonUnSubscribeメソッドが呼ばれる)
	$("#unsubscribe > button").click(function() {
		var select = $("#unsubscribe > select");

		console.log("-- Unsubscribe --");
		console.log("Topic: " + select.val());

		try {
			sess.unsubscribe(select.val());
		} catch(e) {
			console.warn(e);
		}
	});

	// RPC (WampServerのonCallメソッドが呼ばれる)
	$("#rpc > button").click(function() {
		var select = $("#rpc > select");

		console.log("-- RPC --");
		console.log("Method: " + select.val());

		sess.call(select.val()).then(function (result) {
			// do stuff with the result
			console.log(result);
		}, function(error) {
			// handle the error
			console.log(error);
		});
	});

	/**
	 * ping送信
	 * 
	 * TODO: 放置するとコネクションが切れるので、暫定的な対策
	 */
	setInterval(function() {
		console.log('Send ping.');

		sess.call('ping').then(function (result) {
			// do stuff with the result
			console.log(result);
		}, function(error) {
			// handle the error
			console.log(error);
		});
	}, 30000);

});
</script>
