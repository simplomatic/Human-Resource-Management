<template>
	<div class="hrm-tbl-action-wrap">
		<div class="hrm-table-action main hrm-attendance-user-searach-main">
			<form @submit.prevent="search()">
				<input type="text" placeholder="From" name="punch_in" value=""  class="hrm-date-picker-from" id="punch_in" v-hrm-datepicker="" :value="punch_in_date">

				<input type="text" placeholder="To" name="punch_out" value="" class="hrm-date-picker-to" id="punch_out" v-hrm-datepicker="" :value="punch_out_date">

				<select v-if="manageAttendance" class="user_id" name="user_id" id="user_id" v-model="search_user_id">
					<option value="-1">-Select Employee-</option>
					<option v-for="(employee, id) in employessDropDown" :value="id">{{ employee }}</option>
					
				</select>
				

				<input type="submit" value="Find" class="button button-secondary attendance-search-btn">
			</form>
		</div>
	</div>
			
</template>

<script>
	import Mixin from './mixin'

	export default {
		mixins: [Mixin],
		
		data: function() {
			return {
				//punch_in_date: 'sdgfashjfdgsad',
				//punch_out_date: '',
				//search_user_id: '-1'
			}
		},

		computed: {
			manageAttendance () {
				if( hrm_user_can('manage_attendance') ) {
					return true;
				}

				return false;
			},
			employessDropDown () {
				return this.$store.state.attendance.employessDropDown;
			},
			punch_in_date: function() {
				return this.$route.query.punch_in;
			},

			punch_out_date: function() {
				return this.$route.query.punch_out;
			},

			search_user_id: {
				get: function() {
					return this.$route.query.user_id ? this.$route.query.user_id : '-1';
				},

				set: function(val) {
					this.$store.commit( 'attendance/setSearchUserId', val );
				}
			}
		},

		created: function() {
			this.$on( 'hrm_date_picker', this.setdate );
			this.$store.commit( 'attendance/searchMode', {status: true} );
		},
		
		methods: {

			setdate: function(date) {
				if ( date.field == 'datepicker_from' ) {
					this.$store.commit( 'attendance/setPunchInDate', { date: date } );
				}

				if ( date.field == 'datepicker_to' ) {
					this.$store.commit( 'attendance/setPunchOutDate', { date: date } );
				}
			},
			search: function() {
				this.$router.push({ 
					query: { 
						punch_in: this.$store.state.attendance.punch_in_date,
						punch_out: this.$store.state.attendance.punch_out_date,
						user_id: this.$store.state.attendance.search_user_id
					}
				});

				this.getAttendance();
			},
		}
	}
</script>


