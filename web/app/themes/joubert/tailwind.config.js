const plugin = require('tailwindcss/plugin')

const titles = {
  '.title-h1': {
    fontFamily: 'Playfair Display',
    fontSize: "3.875rem",
    lineHeight: '76px',
    fontWeight: 400,
  },
  '.title-h2': {
    fontFamily: 'Playfair Display',
    fontSize: "3.25rem",
    lineHeight: "62px",
    fontWeight: 400,
  },
  '.title-h3': {
    fontFamily: 'Playfair Display',
    fontSize: '2.625rem',
    lineHeight: '48px',
    fontWeight: 600,
  },
  '.title-h4': {
    fontFamily: 'Playfair Display',
    fontSize: '2.375rem',
    lineHeight: '52px',
    fontWeight: 400,
  },
  '.title-h5': {
    fontFamily: "'Lato', sans-serif",
    fontSize: '1.5rem',
    lineHeight: '36px',
    fontWeight: 600,
  },
  '.title-h6': {
    fontFamily: "'Lato', sans-serif",
    fontSize: '1.25rem',
    lineHeight: '28px',
    fontWeight: 700,
  },
  '.title-mobile-h1': {
    fontFamily: 'Playfair Display',
    fontSize: '2.625rem',
    fontWeight: 400,
  },
  '.title-mobile-h2': {
    fontFamily: 'Playfair Display',
    fontSize: '2.75rem',
    fontWeight: 400,
  },
  '.title-mobile-h3': {
    fontFamily: 'Playfair Display',
    fontSize: '1.5rem',
    lineHeight: '32px',
    fontWeight: 600,
  },
  '.title-mobile-h4': {
    fontFamily: 'Playfair Display',
    fontSize: '1.5rem',
    lineHeight: '32px',
    fontWeight: 400,
  },
}

module.exports = {
  content: [
    './app/**/*.php',
    './resources/**/*.{php,vue,js,json}'
  ],
  theme: {
    extend: {
      zIndex: {
        '1' : '1',
        '2' : '2',
        '3' : '3',
        '4' : '4',
        '5' : '5',
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
      },
      container: {
        padding: {
          DEFAULT: '1.75rem',
          sm: '1rem',
        },
      },
      fontFamily: {
        'lato': [
          "'Lato', sans-serif",
        ],
        'playfair': [
          "'Playfair Display', serif",
        ],
      },
      width: {
        '18': '4.5rem',
      },
      height: {
        '18': '4.5rem',
      },
      boxShadow: {
        'contour': 'rgba(0, 0, 0, 0.24) 0px 3px 8px',
      },
      backgroundImage: {
        'cards-filter': 'linear-gradient(180deg, rgba(1,54,58,0) 0%, rgba(1,54,58,1) 100%)',
        'cards-filter-right': 'linear-gradient(70deg, rgba(1,54,58,0) 0%, rgba(1,54,58,0.7) 100%)',
        'cards-filter-left': 'linear-gradient(270deg, rgba(1,54,58,0) 0%, rgba(1,54,58,0.7) 100%)',
        'main-hero-filter': 'linear-gradient(90deg, rgba(0,39,41,0.6026611328125) 0%, rgba(0,39,41,0) 100%)',
        'featured-work': 'linear-gradient(65.61deg, #021723 6.85%, #001F31 81.19%)',
      },
      colors: {
        'green': {
          'primary' : '#03464A',
          'light' : '#C9EBED',
          'lightest' : '#DFE9EA',
          'dark' : '#003235',
          'paragraph' : '#345F62',
          'feat-work-continue': 'rgba(0, 31, 49, 1)',
        },
        'white': {
          'cloud' : '#FBFBFB',
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
