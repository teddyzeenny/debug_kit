<?php
/**
 * SQL Log Panel Element
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org
 * @package       debug_kit
 * @subpackage    debug_kit.views.elements
 * @since         DebugKit 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<h2><?php __d('debug_kit', 'Sql Logs')?></h2>
<?php if (!empty($content)) : ?>
	<?php foreach ($content as $dbName => $queryLog) : ?>
	<div class="sql-log-panel-query-log">
		<h4><?php echo $dbName ?></h4>
		<?php
			$headers = array('Query', 'Error', 'Affected', 'Num. rows', 'Took (ms)', 'Actions');
			if ($toolbar->getName() == 'HtmlToolbar'):
				foreach ($queryLog['queries'] as $i => $row):
					if (!empty($row['actions'])):
						$queryLog['queries'][$i]['actions'] = sprintf(
							'<span class="slow-query">%s</span>',
							__d('debug_kit', 'maybe slow', true)
						);
					endif;
					$queryLog['queries'][$i]['actions'] .= $toolbar->explainLink($row['query'], $dbName);
					$queryLog['queries'][$i]['query'] = h($row['query']);
				endforeach;
			endif;
			echo $toolbar->table($queryLog['queries'], $headers, array('title' => 'SQL Log ' . $dbName));
		 ?>
		<h4><?php __d('debug_kit', 'Query Explain:'); ?></h4>
		<div id="sql-log-explain-query">
			<p><?php __d('debug_kit', 'Click an "Explain" link above, to see the query explaination.'); ?></p>
		</div>
	</div>
	<?php endforeach; ?>
<?php else:
	echo $toolbar->message('Warning', __d('debug_kit', 'No active database connections', true));
endif; ?>

<script type="text/javascript">
//<![CDATA[
DEBUGKIT.module('sqlLog');
DEBUGKIT.sqlLog = function () {
	var Element = DEBUGKIT.Util.Element,
		Request = DEBUGKIT.Util.Request,
		Event = DEBUGKIT.Util.Event,
		Collection = DEBUGKIT.Util.Collection;

	return {
		init : function () {
			var sqlPanel = document.getElementById('sql_log-tab');
			var buttons = sqlPanel.getElementsByTagName('A');

			// Button handling code for explain links.
			// performs XHR request to get explain query.
			var handleButton = function (event) {
				event.preventDefault();
				var fetch = new Request({
					onComplete : function (response) {
						var targetEl = document.getElementById('sql-log-explain-query');
						targetEl.innerHTML = response.response.text;
					},
					onFail : function () {
						alert('Could not fetch EXPLAIN for query.');
					}
				}).send(this.href);
			};
	
			Collection.apply(buttons, function (button, i) {
				if (Element.hasClass(button, 'sql-explain-link')) {
					Event.addEvent(button, 'click', handleButton);
				}
			});
		}
	};
}();
DEBUGKIT.loader.register(DEBUGKIT.sqlLog);
//]]>
</script>