const plugin = require('tailwindcss/plugin')

const titles = {
  '.title-h1': {
    fontFamily: 'Optima',
    fontSize: "clamp(2.25rem, calc(2.25rem + (5 - 2.25) * ((100vw - 16rem) / (64 - 16))), 5rem)",
    lineHeight: 'normal',
    paddingBottom: '0.5rem',
    marginBottom: '0rem',
    fontWeight: 700,
  },
  '.title-h2': {
    fontFamily: 'Optima Bold',
    fontSize: "clamp(2.15rem, calc(2.15rem + (3.5 - 2.15) * ((100vw - 16rem) / (64 - 16))), 3.5rem)",
    lineHeight: "normal",
    paddingBottom: '1rem',
    marginBottom: '0rem',
  },
  '.title-h3': {
    fontFamily: 'Optima Bold',
    fontSize: 'clamp(2rem, calc(2rem + (3 - 2) * ((100vw - 16rem) / (64 - 16))), 3rem)',
    lineHeight: 'normal',
    fontWeight: 'inherit',
    paddingBottom: '0.875rem',
    marginBottom: '0rem',
  },
  '.title-h4': {
    fontFamily: 'Optima Bold',
    fontSize: '2rem',
    lineHeight: '2.75rem',
    marginBottom: '0.25rem',
    marginTop: '1.5rem',
  },
  '.title-h5': {
    fontFamily: "'Lato', sans-serif",
    fontSize: '1.5rem',
    lineHeight: 'normal',
    fontWeight: 900,
  },
  '.title-h6': {
    fontFamily: "'Lato', sans-serif",
    fontSize: '1.25rem',
    lineHeight: 'normal',
    fontWeight: 700,
  },
  '.title-mobile-h1': {
    fontFamily: 'Optima Bold',
    fontSize: "2.25rem",
    lineHeight: "normal",
  },
  '.title-mobile-h2': {
    fontFamily: 'Optima Bold',
    fontSize: "2.15rem",
    lineHeight: "normal",
  },
  '.title-mobile-h3': {
    fontFamily: 'Optima Bold',
    fontSize: "2rem",
    lineHeight: "normal",
  },
}

module.exports = {
  content: [
    './app/**/*.php',
    './resources/**/*.{php,vue,js,json}'
  ],
  theme: {
    extend: {
      blur: {
        'xxxl': '277px',
      },
      padding: {
        '13': '3.375rem',
      },
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
      },
      letterSpacing: {
        widest: '0.03125rem',
        surtitle: '3px',
      },
      container: {
        padding: {
          DEFAULT: '1rem',
          sm: '0.3rem',
        },
      },
      fontFamily: {
        'lato': [
          "'Lato', sans-serif",
        ],
        'optima-bold': [
          'Optima Bold',
        ],
      },
      fontSize: {
        '5xxl': '2.8125rem',
        '11xl': '11.25rem',
      },
      boxShadow: {
        '100': '0px 2px 20px rgba(125, 129, 141, 0.08)',
        '200': '0px 2px 30px rgba(48, 70, 89, 0.15)',
        'primary': '#fff -10px 10px 0px -1px, #EE3831 -10px 10px',
        'primary-hover': '#fff -10px 10px 0px -1px, #FF605A -10px 10px',
        'contour': 'rgba(0, 0, 0, 0.24) 0px 3px 8px',
      },
      backgroundImage: {
        'cards': 'linear-gradient(180deg, rgba(0, 0, 0, 0) 41.67%, rgba(0, 0, 0, 0.67) 100%), linear-gradient(180deg, rgba(0, 0, 0, 0) 59.9%, rgba(0, 0, 0, 0.3) 100%)',
      },
      colors: {
        'red': {
          '0' : '#EE3831',
          '100' : '#FF605A',
          '200' : '#FF928D',
          '300' : '#FFD3D1',
        },
        'yellow-pge': '#F2B754',
        'orange-pge': '#F19558',
        'green-pge': '#5AB2AA',
        'blue-pge': '#357AFF',
        'blue-light-pge': 'rgba(53, 122, 255, 0.2)',
        'grey': {
          0: '#000000',
          100: '#313131',
          200: '#7D818D',
          300: '#F1F1FA',
          400: '#F8F8FD',
          500: '#FFFFFF',
          1010: '#101010',
        },
      },
      typography: (theme) => ({
        DEFAULT: {
          css: {
            maxWidth: '100%',
            p: {
              lineHeight: '1.25',
              marginBottom: '1rem',
              marginTop: '1rem',
              strong: {
              color: 'inherit',
              },
              a: {
                fontWeight: '700',
                button: {
                  fontWeight: '500',
                },
              },
            },
            ul: {
              listStyle: 'none',
              marginTop: '1.25rem',
              marginBottom: '1.25rem',
              paddingLeft: '1.25em',
              li:{
                marginTop: '0',
                marginBottom: '1rem',
                lineHeight: '1.15',
                a: {
                  display: 'inline-block',
                  fontWeight: '700',
                  marginTop: '0rem',
                  marginBottom: '0rem',
                  button: {
                    fontWeight: '500',
                  },
                },
                '&:before': {
                  content: '"\\2022"',
                  color: theme('colors.grey.200'),
                  fontWeight: 'bold',
                  display: 'inline-block',
                  width: '1em',
                  marginLeft: '-1em',
                },
                ul: {
                  marginTop: '0',
                  marginBottom: '0',
                  li: {
                    lineHeight: '1.25',
                    marginTop: '0',
                    marginBottom: '0',
                  },
                },
              },
            },
            a: {
              textDecoration: 'underline',
              display: 'block',
              width: 'fit-content',
              color: theme('colors.red.100'),
              '&:hover': {
                color: theme('colors.red.100'),
              },
              button: {
                fontWeight: '500',
              },
            },
            'h1, h2, h3, h4, h5': {
              color: 'inherit',
            },
            h1: {
              ...titles[".title-h1"]
            },
            h2: {
              ...titles[".title-h2"]
            },
            h3: {
              ...titles[".title-h3"]
            },
            h4: {
              ...titles[".title-h4"]
            },
            h5: {
              ...titles[".title-h5"]
            },
            h6: {
              ...titles[".title-h6"]
            },
          },
        },
      }),
    },
  },
  plugins: [
    require('@tailwindcss/typography')({
      className: 'wysiwyg-content',
    }),
    plugin(function ({addComponents, theme}) {
      addComponents({
        ...titles
      })
    })
  ],
};
