export default {
	methods: {
		getPersonalInfo (args) {
			var self = this;
			
            var request_data = {
                data: {
                    employee_id: args.employee_id
                },
                success: function(res) {
                	self.$store.commit('profile/setPersonalInfo', res);
                	if (typeof args.callback === 'function') {
                        args.callback(res);
                    } 
                }
            };

            self.httpRequest('hrm_get_personal_info', request_data);
		},

        getJobLocation (args) {
            var self = this;
            
            var request_data = {
                data: {
                    employee_id: args.employee_id
                },
                success: function(res) {
                    self.$store.commit('profile/setJobLocation', res.data);
                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    } 
                }
            };

            self.httpRequest('hrm_get_employee_job_location', request_data);
        }
	}
}