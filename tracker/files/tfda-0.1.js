/**
 * Tiny Finite Deterministic Automaton.
 */

var fda = {
	'state': undefined,
	'data': {
		's_start': {
			'transitions': {},
			'actions': {}
		}
	},

	'addTransition': function(state1, signal, state2, action) {
		if (fda.data[state1]==undefined)
		{
			fda.data[state1] = {
				'transitions': {},
				'actions': {}
			};
		}

		fda.data[state1].transitions[signal] = state2;

		if (action!=undefined)
			fda.data[state1].actions[signal] = action;
	},

	'start': function() {
		fda.state = 's_start';
		return(true);
	},

	'next': function(signal) {
		if (signal && fda.state!=undefined && fda.state!='s_end' && fda.data[fda.state]!=undefined && fda.data[fda.state].transitions[signal]!=undefined)
		{
			if (fda.data[fda.state].actions[signal]!=undefined)
				fda.data[fda.state].actions[signal]();
			fda.state = fda.data[fda.state].transitions[signal]
			return(true);
		}
		return(false);
	},

	'started': function() { return(fda.state!=undefined); },
	'finished': function() { return(fda.state=='s_end'); }
};
