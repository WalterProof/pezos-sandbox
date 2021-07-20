module.exports = {
    purge: [],
    darkMode: 'class', // or 'media' or 'class'
    theme: {
        container: {
            center: true,
            padding: '2rem',
            screens: {
                sm: '100%',
                md: '100%',
                lg: '100%',
                xl: '960px',
            },
        },
        extend: {},
    },
    variants: {
        extend: {},
    },
    plugins: [require('@tailwindcss/forms')],
};
