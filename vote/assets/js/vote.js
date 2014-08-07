
function Vote(items)
{	
	var self = this;
	var editor;
	this.classes = {
		warning: 'bg-warning',
		success: 'bg-success',
		information: 'bg-info',
		error: 'bg-danger',
		hidden: 'message-hidden',
	};
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

	this.init = function (containerId) {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method](containerId);
			}
		});
	}
	
	this.initVote = function (containerId) {
		var container = $nitm.getObj((containerId == undefined) ? 'body' : containerId);
		this.elements.allowVote.map(function (v) {
			$(container).find("[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					self.operation(this);
				});
			})
		});
	}
	
	this.operation = function (form) {
		data = $(form).serializeArray();
		data.push({'name':'__format', 'value':'json'});
		data.push({'name':'getHtml', 'value':true});
		data.push({'name':'do', 'value':true});
		data.push({'name':'ajax', 'value':true});
		switch(!$(form).attr('href'))
		{
			case false:
			var request = $nitm.doRequest($(form).attr('href'), 
				data,
				function (result) {
					self.afterVote(result);
				},
				function () {
					$nitm.notify('Error Could not perform Vote action. Please try again', self.classes.error, false);
				},
				function () {
					$nitm.notify('Error Could not perform Vote action. Please try again', self.classes.error, false);
				},
				null,
				true
			);
			break;
		}
	}
	
	this.afterVote = function (result) {
		if(result.success)
		{
			var $down = $nitm.getObj(self.elements.vote.down+result.id);
			var $up = $nitm.getObj(self.elements.vote.up+result.id);
			switch(result.atMin)
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
					$up.show('slow');
					$up.click($up.attr('oldonclick'));
					break;
				}
				break;
			}
			try {
				$nitm.getObj('vote-value-positive'+result.id).html(Math.round(result.value.positive));
				$nitm.getObj('indicator'+result.id).css('background', 'rgba(255,51,0,'+result.value.positive+')');
				$nitm.getObj('vote-value-negative'+result.id).html(Math.round(result.value.negative));
				$nitm.getObj("[role~='voteIndicator"+result.id+"']").css('background-color', 'rgba(255,51,0,'+result.value.ratio+')');
			}catch(error) {
				try {
					$nitm.getObj('percent'+result.id).html(Math.round(result.value.positive));
					$nitm.getObj("[role~='voteIndicator"+result.id+"']").css('background-color', 'rgba(255,51,0,'+result.value.ratio+')');
				}catch(error) {}
			}
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.initModule('vote', new Vote());
});