(function () {
    var validationRules = {
        db_name: {
            identifier: 'db[name]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please provide a valid database name'
                }
            ]
        },
        db_host: {
            identifier: 'db[host]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Database\'s host cannot be empty'
                }
            ]
        },
        db_port: {
            identifier: 'db[port]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Database\'s port cannot be empty'
                }
            ]
        },
        db_username: {
            identifier: 'db[username]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Database\'s username cannot be empty'
                }
            ]
        },
        admin_full_name: {
            identifier: 'admin[full_name]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please enter your full name'
                }
            ]
        },
        admin_email: {
            identifier: 'admin[email]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'You must provide a valid email'
                },
                {
                    type: 'email',
                    prompt: 'The email you\'ve entered is not a valid email address'
                }
            ]
        },
        username: {
            identifier: 'admin[username]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please choose a username'
                }
            ]
        },
        admin_password: {
            identifier: 'password',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please enter a password'
                },
                {
                    type: 'length[6]',
                    prompt: 'Your password must be at least 6 characters'
                }
            ]
        },
        admin_password_confirm: {
            identifier: 'admin[password]',
            rules: [
                {
                    type: 'empty',
                    prompt: 'Please confirm the password'
                },
                {
                    type: 'match[password]',
                    prompt: 'The password does not match'
                }
            ]
        }
    };
    
    $('form').form(validationRules, {
        inline: true,
        on: 'blur'
    });
})();