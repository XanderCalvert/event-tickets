/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import Template from './template';
import { withStore } from '@moderntribe/common/hoc';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';

const mapStateToProps = ( state ) => ( {
	isDisabled: selectors.getRSVPIsLoading( state ) || selectors.getRSVPSettingsOpen( state ),
	tempCapacity: selectors.getRSVPTempCapacity( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	onTempCapacityChange: ( e ) => {
		dispatch( actions.setRSVPTempCapacity( e.target.value ) );
		dispatch( actions.setRSVPHasChanges( true ) );
	},
} );

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
)( Template );
