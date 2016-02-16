'use strict';

class Vote extends NitmEntity
{
	constructor() {
		super('vote');
		//The colors. With 1 being positive and 0 being negative color
		this.colors = ['51, 192, 0', '192, 51, 0'];
		this.views = {
			containers: {
				vote: 'vote',
			}
		};
		this.elements = {
			allowVote: ['voteUp', 'voteDown'],
			vote: {
				up: 'vote-up',
				down: 'vote-down',
			},
			actions : {
				up: '/vote/up',
				down: '/vote/down',
			},
		};
		this.defaultInit = [
			'initVote',
		];
	}

	initVote(containerId) {
		let container = $nitm.getObj((containerId === undefined) ? 'body' : containerId);
		this.elements.allowVote.map((v) => {
			$(container).find("[role='"+v+"']").map((elem, i) => {
				let $elem = $(elem);
				if($elem.data('nitm-vote') === true)
					return;
				$elem.data('nitm-vote', true);
				$elem.on('click', function (e) {
					e.preventDefault();
					this.operation($elem);
				});
			});
		});
	};

	operation(form) {
		let data = $(form).serializeArray();
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!$(form).attr('href'))
		{
			case false:
			let request = $nitm.doRequest($(form).attr('href'),
				data,
				function (result) {
					this.afterVote(result);
				},
				function () {
					$nitm.notify('Error Could not perform Vote action. Please try again', this.classes.error, false);
				},
				function () {
					$nitm.notify('Error Could not perform Vote action. Please try again', this.classes.error, false);
				}
			);
			break;
		}
	};

	afterVote(result) {
		if(result.success)
		{
			let $down = $nitm.getObj(this.elements.vote.down+result.id);
			let $up = $nitm.getObj(this.elements.vote.up+result.id);
			$up.toggleClass(result.class.up);
			$down.toggleClass(result.class.down);
			/*switch(result.atMin)
			{
				case true:
				//Hide the downvote button
				$down.hide('slow');
				$down.attr('oldonclick', $down.attr('onclick'));
				$down.click(void(0));
				break;

				default:
				switch($down.css('display'))
				{
					case 'none':
					$down.show('slow');
					$down.click($up.attr('oldonclick'));
					break;
				}
				break;
			}
			switch(result.atMax)
			{
				case true:
				//Hide the upvote button
				$up.hide('slow');
				$up.attr('oldonclick', $down.attr('onclick'));
				$up.click(void(0));
				break;

				default:
				switch($up.css('display'))
				{
					case 'none':
					//$up.show('slow');
					$up.click($up.attr('oldonclick'));
					break;
				}
				break;
			}*/
			let bgColor = this.colors[~~!(result.value.positive > result.value.negative)];
			try {
				$nitm.getObj('vote-value-positive'+result.id).html(Math.round(result.value.positive));
				$nitm.getObj('indicator'+result.id).css('background', 'rgba(255,51,0,'+result.value.positive+')');
				$nitm.getObj('vote-value-negative'+result.id).html(Math.round(result.value.negative));
				$nitm.getObj("[role~='voteIndicator"+result.id+"']").css('background-color', 'rgba('+bgColor+','+result.value.ratio+')');
			} catch(e) {
				try {
					$nitm.getObj('percent'+result.id).html(Math.round(result.value.positive));
					$nitm.getObj("[role~='voteIndicator"+result.id+"']").css('background-color', 'rgba('+bgColor+','+result.value.ratio+')');
				} catch(e) {}
			}
		}
	};
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Vote());
});
