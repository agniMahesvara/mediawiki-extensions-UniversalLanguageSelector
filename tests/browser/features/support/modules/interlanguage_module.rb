require "page-object"

module InterlanguagePageModule
  include PageObject

  a(:add_links, id: 'wbc-linkToItem-link')
  span(:apply_settings, class: 'uls-settings-trigger')
  a(:back_to_display, text: 'Back to display settings')
  a(:back_to_input, text: 'Back to input settings')
  button(:cancel, class: 'button uls-display-settings-cancel')
  span(:cog, class: 'uls-settings-trigger')
  button(:ellipsis_button, class: 'uls-more-languages button')
  a(:english_link, text: 'English')
  div(:input_settings, id: 'input-settings-block')
	div(:language_list, class: 'row uls-language-list lcd')
  text_field(:language_search, id: 'languagefilter')
  button(:non_default_language, class: 'button uls-language-button', index: 1)
  a(:talk, text: 'Discussion')
  span(:x, id: 'languagesettings-close')
  a(:how_to_use_ml_transliteration, href: 'https://www.mediawiki.org/wiki/Special:MyLanguage/Help:Extension:UniversalLanguageSelector/Input_methods/ml-transliteration')
end