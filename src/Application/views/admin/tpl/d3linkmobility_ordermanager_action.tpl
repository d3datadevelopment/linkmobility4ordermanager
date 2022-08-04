[{block name="ordermanager_admin_action_linkmobilitymessage"}]
    <dl class="[{$blActionRestriction}]">
        [{include file="d3ordermanager_activeswitch.tpl" oActionRequ=$oAction blActionRestriction=$blActionRestriction readonly=$readonly}]
        <dd>
            [{if $oView->isEditMode()}]
                [{block name="ordermanager_admin_action_linkmobilitymessage_editor"}]
                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM1"}]<br>
                    <input type="radio" id="FromSourceTpl" name="value[sLinkMobilityMessageFromSource]" value="template" [{if $edit->getValue('sLinkMobilityMessageFromSource') == 'template'}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]>
                    <label for="FromSourceTpl">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE"}]</label>
                    <br>
                    <label style="margin-left: 15px;" for="FromTplFile">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_SOURCE"}]</label><input id="FromTplFile" type="text" name="value[sLinkMobilityMessageFromTemplatename]" size="50" maxlength="250" value="[{$edit->getValue('sLinkMobilityMessageFromTemplatename')}]" [{$blActionRestriction}] [{$readonly}]> [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_DESC"}]<br>
                    <input style="margin-left: 20px;" id="FromThemeAdmin" type="radio" name="value[sLinkMobilityMessageFromTheme]" value="admin" [{if $edit->getValue('sLinkMobilityMessageFromTheme') == 'admin'}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]> <label for="FromThemeAdmin">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_ADMIN"}] ([{$oView->getManagerTemplateDirs(1)}])</label><br>
                    <input style="margin-left: 20px;" id="FromThemeFrontend" type="radio" name="value[sLinkMobilityMessageFromTheme]" value="frontend" [{if $edit->getValue('sLinkMobilityMessageFromTheme') == 'frontend'}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]> <label for="FromThemeFrontend">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_FRONTEND"}] ([{$oView->getManagerTemplateDirs(0)}])</label><br>
                    <input style="margin-left: 20px;" id="FromModule" type="radio" name="value[sLinkMobilityMessageFromTheme]" value="module" [{if $edit->getValue('sLinkMobilityMessageFromTheme') == 'module'}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]>
                    <label for="FromModule">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_MODULE"}]</label>
                    <label for="FromModuleId" style="position: absolute; left: -2000px">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE"}]</label>
                    <select id="FromModuleId" class="editinput" name="value[sLinkMobilityMessageFromModulePath]" size="1" [{$blActionRestriction}] [{$readonly}]>
                        [{foreach from=$oView->getModulePathList() key="sId" item="sModulePath"}]
                            <option value="[{$sId}]" [{if $edit->getValue('sLinkMobilityMessageFromModulePath') == $sId}]selected[{/if}]>[{$sModulePath}]</option>
                        [{/foreach}]
                    </select> [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_DESC"}]<br>
                    <hr>
                    <input type="radio" id="FromSourceCms" name="value[sLinkMobilityMessageFromSource]" value="cms" [{if $edit->getValue('sLinkMobilityMessageFromSource') == 'cms'}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]> <label for="FromSourceCms">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS"}]</label>
                    <br>
                    <label for="FromCmsHtml" style="margin-left: 15px;">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS_SOURCE"}]</label>
                    <SELECT id="FromCmsHtml" class="editinput" name="value[sLinkMobilityMessageFromContentname]" size="1" [{$blActionRestriction}] [{$readonly}]>
                        [{foreach from=$oView->getContentList() item="oContent"}]
                            <option value="[{$oContent->getId()}]" [{if $edit->getValue('sLinkMobilityMessageFromContentname') == $oContent->getId()}]selected[{/if}]>[{$oContent->getFieldData('oxtitle')}] ([{$oContent->getFieldData('oxloadid')}])</option>
                        [{/foreach}]
                    </SELECT> <br>

                    <hr>

                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM2"}]<br>

                    <input type="hidden" name="value[blLinkMobilityMessageToCustomer]" value="0">
                    <input id="ToCustomer" class="edittext ext_edittext" type="checkbox" name="value[blLinkMobilityMessageToCustomer]" value='1' [{if $edit->getValue('blLinkMobilityMessageToCustomer') == 1}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]>
                    <label for="ToCustomer">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER"}]</label>
                    [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER_DESC"}]<br>
                    <input type="hidden" name="value[blLinkMobilityMessageToCustom]" value="0">
                    <input id="ToMail" class="edittext ext_edittext" type="checkbox" name="value[blLinkMobilityMessageToCustom]" value='1' [{if $edit->getValue('blLinkMobilityMessageToCustom') == 1}]checked[{/if}] [{$blActionRestriction}] [{$readonly}]>
                    <label for="ToMail">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL"}]</label>
                    <label for="ToMailInput" style="position: absolute; left: -2000px">[{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE"}]</label>
                    <input id="ToMailInput" type="text" name="value[sLinkMobilityMessageToCustomAddress]" size="30" maxlength="60" value="[{$edit->getValue('sLinkMobilityMessageToCustomAddress')}]" [{$blActionRestriction}] [{$readonly}]>
                    [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL_DESC"}]

                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings"}] [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings_DESC"}]<br>
                [{/block}]
            [{else}]
                [{block name="ordermanager_admin_action_LinkMobilityMessage_viewer"}]
                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM1"}]<br>
                    [{if $edit->getValue('sLinkMobilityMessageFromSource') == 'template'}]
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE"}]
                        <br>
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_SOURCE"}] [{$edit->getValue('sLinkMobilityMessageFromTemplatename')}] [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTEMPLATE_DESC"}]<br>
                        [{if $edit->getValue('sLinkMobilityMessageFromTheme') == 'admin'}]
                            [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_ADMIN"}] ([{$oView->getManagerTemplateDirs(1)}])
                        [{elseif $edit->getValue('sLinkMobilityMessageFromTheme') == 'frontend'}]
                            [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_FRONTEND"}] ([{$oView->getManagerTemplateDirs(0)}])
                        [{elseif $edit->getValue('sLinkMobilityMessageFromTheme') == 'module'}]
                            [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_MODULE"}] [{$edit->getValue('sLinkMobilityMessageFromModulePath')}] [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMTPL_DESC"}]
                        [{/if}]
                    [{elseif $edit->getValue('sLinkMobilityMessageFromSource') == 'cms'}]
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS"}]
                        <br>
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROMCMS_SOURCE"}]
                        [{foreach from=$oView->getContentList() item="oContent"}]
                            [{if $edit->getValue('sLinkMobilityMessageFromContentname') == $oContent->getId()}]
                                [{$oContent->getFieldData('oxtitle')}] ([{$oContent->getFieldData('oxloadid')}])
                            [{/if}]
                        [{/foreach}]
                    [{/if}]
                    <hr>

                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_FROM2"}]<br>

                    [{if $edit->getValue('blLinkMobilityMessageToCustomer') == 1}]
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER"}]
                        [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOCUSTOMER_DESC"}],
                    [{/if}]
                    [{if $edit->getValue('blLinkMobilityMessageToCustom') == 1}]
                        [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL"}]
                        "[{$edit->getValue('sLinkMobilityMessageToCustomAddress')}]"
                        [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_TOMAIL_DESC"}]
                    [{/if}]

                    [{oxmultilang ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings"}] [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_ToUseLMSettings_DESC"}]<br>
                [{/block}]
            [{/if}]
            [{oxinputhelp ident="D3_ORDERMANAGER_ACTION_LINKMOBILITYMESSAGE_DESC"}]
        </dd>
        <div class="spacer"></div>
    </dl>
[{/block}]