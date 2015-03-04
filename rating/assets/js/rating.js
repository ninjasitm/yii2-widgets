
function Rating()
{	
	NitmEntity.call(this, arguments);
	var self = this;
	var editor;
	this.id = 'rating';
	this.classes = {
		warning: 'bg-warning',
		success: 'bg-success',
		information: 'bg-info',
		error: 'bg-danger',
		hidden: 'message-hidden',
	};
	this.views = {
		containers: {
				rating: 'rating',
				upVote: 'rating-up',
				downVote: 'rating-down',
		}
	};
	this.elements = {
		allowRating: ['ratingUp', 'ratingDown'],
		vote: {
			up: 'rate-up',
			down: 'rate-down',
		},
		actions : {
			up: '/rating/up',
			down: '/rating/down',
		},
	};
	this.defaultInit = [
		'initRating',
	];

	this.init = function () {
		this.defaultInit.map(function (method, key) {
			if(typeof self[method] == 'function')
			{
				self[method]();
			}
		});
	}
	
	this.initRating = function (container) {
		var container = (container == undefined) ? 'body' : container;
		this.elements.allowRating.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$nitm.module('entity').operation(this);
				});
			})
		});
	}
	
	this.afterRating = function (result) {
		if(result.success)
		{
			var $down = $nitm.getObj(self.elements.vote.down+result.id);
			var $up = $nitm.getObj(self.elements.vote.up+result.id);
			switch(result.at)
			{
				case 'max':
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
			switch(result.at)
			{
				case 'min':
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

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Rating());
});