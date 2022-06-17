const { spacing } = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');
const path = require('path')
const fs = require('fs')

/**
 * Finds and lists all files in a directory with a specific extension
 * https://gist.github.com/victorsollozzo/4134793
 * @return Array
 */
function recFindByExt(base,ext, files,result) {
    files = files || fs.readdirSync(base)
    result = result || []

    files.forEach(
        function (file) {
            const newbase = path.join(base,file);
            if (fs.statSync(newbase).isDirectory()) {
                result = recFindByExt(newbase, ext, fs.readdirSync(newbase), result)
            } else {
                if (file.substr(-1*(ext.length+1)) == '.' + ext) {
                    result.push(newbase)
                }
            }
        }
    )
    return result
}

/**
 * Returns an array of all files to be used in tailwind purge.content
 */
const purgeContent = () => {
    // Add any sub-directories you wish to be excluded by Tailwind when checking
    // the hyva-default theme.
    // For example you may have removed Magento_Review from your store, and therefore
    // do not wish for Tailwind to generate any CSS for it.
    const EXCLUDE_FROM_PARENT = []; // e.g. ['Magento_Review']

    // Declare array to stores all paths for hyvaDefault theme's phtml files
    let hyvaDefault = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-default-theme/', 'phtml');

    let hyvaWidgets = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-hyva-widgets/src/', 'phtml');

    let hyvaMageworx = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-mageworx-advancedproductoptions/src/', 'phtml');

    let hyvaWeltpixel = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-weltpixel-google-tag-manager/src/', 'phtml');


    // Hyva checkout components
    let hyvaReact = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-react-checkout/src/reactapp/src/','jsx');
    let hyvaReactContainer = recFindByExt('../../../../../../../vendor/hyva-themes/magento2-react-checkout/src/view/frontend/templates/','phtml');

    // Declare array to stores all paths for your current theme's phtml files

    let themeXml = recFindByExt('../../','xml');
    let currentTheme = recFindByExt('../../','phtml');

    currentTheme.concat(hyvaWidgets);
    currentTheme.concat(hyvaMageworx);
    currentTheme.concat(hyvaWeltpixel);
    currentTheme.concat(themeXml);
    currentTheme.concat(hyvaReact);
    currentTheme.concat(hyvaReactContainer);



    // Filter the array of templates from hyva-default to remove any templates overridden in your theme.
    // A similar filter can be used on other parent theme's if you have a
    // multi-store Magento install using a different theme structure.
    hyvaDefault = hyvaDefault.filter(function(item) {
        let isAllowed = true;

        for(const key in this) {
            if (item.includes(this[key].replace('/..//g', ''))) {
            isAllowed = false;
        }
    }

    return isAllowed;
}, currentTheme.concat(EXCLUDE_FROM_PARENT));

return currentTheme.concat(hyvaDefault);
}

module.exports = {
    mode: process.env.TAILWIND_COMPILE_MODE || 'jit', // either 'jit' or 'aot'
    theme: {
        extend: {
            spacing: {
                '50per': '50%',
            },
            screens: {
                'sm': '640px',
                // => @media (min-width: 640px) { ... }
                'md': '768px',
                // => @media (min-width: 768px) { ... }
                'lg': '1024px',
                // => @media (min-width: 1024px) { ... }
                'xl': '1280px',
                // => @media (min-width: 1280px) { ... }
                '2xl': '1536px',
                // => @media (min-width: 1536px) { ... }
            },
            colors: {
                primary: {
                    lighter: colors.red['700'],
                    "DEFAULT": colors.red['800'],
                    darker: colors.red['900'],
                },
                secondary: {
                    lighter: colors.yellow['400'],
                    "DEFAULT": colors.yellow['500'],
                    darker: colors.yellow['600'],
                },
                background: {
                    lighter: colors.lime['100'],
                    "DEFAULT": colors.lime['200'],
                    darker: colors.lime['300'],
                }
            },
            textColor: {
                orange: colors.orange,
                primary: {
                    lighter: colors.black['700'],
                    "DEFAULT": colors.black['800'],
                    darker: colors.black['900'],
                },
                secondary: {
                    lighter: colors.black['400'],
                    "DEFAULT": colors.black['600'],
                    darker: colors.black['800'],
                },
            },
            backgroundColor: {
                primary: {
                    lighter: colors.white['600'],
                    "DEFAULT": colors.white['700'],
                    darker: colors.white['800'],
                },
                secondary: {
                    lighter: colors.white['100'],
                    "DEFAULT": colors.white['200'],
                    darker: colors.white['300'],
                },
                container: {
                    lighter: '#ffffff',
                    "DEFAULT": '#fafafa',
                    darker: '#f5f5f5',
                }
            },
            borderColor: {
                primary: {
                    lighter: colors.gray['600'],
                    "DEFAULT": colors.gray['700'],
                    darker: colors.gray['800'],
                },
                secondary: {
                    lighter: colors.gray['100'],
                    "DEFAULT": colors.gray['200'],
                    darker: colors.gray['300'],
                },
                container: {
                    lighter: '#f5f5f5',
                    "DEFAULT": '#e7e7e7',
                    darker: '#b6b6b6',
                }
            },
            minWidth: {
                8: spacing["8"],
                20: spacing["20"],
                40: spacing["40"],
                48: spacing["48"],
            },
            minHeight: {
                14: spacing["14"],
                'screen-25': '25vh',
                'screen-50': '50vh',
                'screen-75': '75vh',
            },
            maxHeight: {
                '0': '0',
                'screen-25': '25vh',
                'screen-50': '50vh',
                'screen-75': '75vh',
            },
            container: {
                center: true,
                padding: '1.5rem'
            }
        }
    },
    variants: {
        extend: {
            borderWidth: ['last', 'hover', 'focus'],
            margin: ['last'],
            opacity: ['disabled', 'group-hover', 'group-focus'],
            backgroundColor: ['even', 'odd'],
            ringWidth: ['active'],
            pointerEvents: ['group-hover']
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ],
    purge: {
        // Examples for excluding patterns from purge
        // options: {
        //     safelist: [/^bg-opacity-/, /^-?[mp][trblxy]?-[4,8]$/, /^text-shadow/],
        // },
        content: purgeContent()
    }
}
