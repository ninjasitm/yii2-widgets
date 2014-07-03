
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
				upVote: 'vote-up',
				downVote: 'vote-down',
		}
	};
	this.elements = {
		allowVote: ['voteUp', 'voteDown'],
		actions : {
			up: '/vote/up',
			down: '/vote/down',
		},
	};
	this.defaultInit = [
					'initVote',
				];

	this.init = function () {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method]();
			}
		});
	}
	
	this.initVote = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.elements.allowVote.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				alert('here');
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
		switch(!$(form).attr('action'))
		{
			case false:
			var request = doRequest($(form).attr('href'), 
					data,
					function (result) {
						self.aftervote(result);
					},
					function () {
						notify('Error Could not perform Vote action. Please try again', self.classes.error, false);
					}
				);
				break;
		}
	}
	
	this.afterVote = function (result) {
		if(result.success)
		{
			switch(result.atMin)
			{
				case true:
				//Hide the downvote button
				getObj(upid).hide('slow');
				getObj(upid).attr('oldonclick', getObj(downid).attr('onclick'));
				getObj(upid).click(void(0));
				break;
				
				default:
				switch(getObj(upid).css('display'))
				{
					case 'none':
					getObj(upid).show('slow');
					getObj(upid).click(getObj(downid).attr('oldonclick'));
					break;
				}
				break;
			}
			switch(result.atMax)
			{
				case true:
				//Hide the upvote button
				getObj(downid).hide('slow');
				getObj(downid).attr('oldonclick', getObj(downid).attr('onclick'));
				getObj(downid).click(void(0));
				break;
				
				default:
				switch(getObj(downid).css('display'))
				{
					case 'none':
					getObj(downid).show('slow');
					getObj(downid).click(getObj(downid).attr('oldonclick'));
					break;
				}
				break;
			}
			try {
				getObj('percent'+id).html(Math.round(result['score']*100));
				getObj('indicator'+id).css('background', 'rgba(255,51,0,'+result['score']+')');
			}catch(error) {}
		}
	}
}

$nitm.addOnLoadEvent(function () {
	$nitm.initModule('vote', new Vote());
});