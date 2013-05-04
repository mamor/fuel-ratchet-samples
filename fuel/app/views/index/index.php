<h1>HOME</h1>

<hr />

<table class="table">
	<thead>
		<tr>
			<th style="width: 25%;">Name</th>
			<th style="width: 25%;">Type</th>
			<th style="width: 50%;">Url</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Single ChatRoom</td>
			<td>WsServer</td>
			<td><?php echo Html::anchor('ratchet/ws/chat/join'); ?></td>
		</tr>
		<tr>
			<td>API Console</td>
			<td>WampServer</td>
			<td><?php echo Html::anchor('ratchet/wamp/api'); ?></td>
		</tr>
	</tbody>
</table>

