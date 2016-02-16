'use strict';

class Rating extends NitmEntity
{
	constructor() {
		super('rating');
		Object.assign(this.views, {
			containers: {
					rating: 'rating',
					upVote: 'rating-up',
					downVote: 'rating-down',
			}
		});
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
	}

	initRating(container) {
		let container = (container == undefined) ? 'body' : container;
		this.elements.allowRating.map(function (v) {
			$(container+" "+"[role='"+v+"']").map(function() {
				$(this).on('click', function (e) {
					e.preventDefault();
					$nitm.module('entity').operation(this);
				});
			})
		});
	}

	afterRating(result) {
		if(result.success)
		{
			let $down = $nitm.$nitm.getObj(self.elements.vote.down+result.id);
			let $up = $nitm.$nitm.getObj(self.elements.vote.up+result.id);
			switch(result.at)
			{
				case 'max':
				$up.hide('slow');
				$up.attr('oldonclick', $down.attr('onclick'));
				$up.click(void(0));
				break;

				default:
				switch($up.css('display'))
				{
					case 'none':
					$up.show('slow');
					$up.click($down.attr('oldonclick'));
					break;
				}
				break;
			}
			switch(result.at)
			{
				case 'min':
				$down.hide('slow');
				$down.attr('oldonclick', $down.attr('onclick'));
				$down.click(void(0));
				break;

				default:
				switch($down.css('display'))
				{
					case 'none':
					$down.show('slow');
					$down.click($down.attr('oldonclick'));
					break;
				}
				break;
			}
			try {
				$nitm.getObj('percent'+id).html(Math.round(result['score']*100));
				$nitm.getObj('indicator'+id).css('background', 'rgba(255, 51, 0, '+result.score+')');
			} catch(error) {}
		}
	}
}

$nitm.onModuleLoad('entity', function (module) {
	module.initModule(new Rating());
});
