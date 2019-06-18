import { isNull } from "util";
import { isDefined } from "../../helper/utils";
import TranslationService from "services/TranslationService";

const NotificationService = require("services/NotificationService");

Vue.component("variation-select", {

    props: {
        template:
        {
            type: String,
            default: "#vue-variation-select"
        }
    },

    data()
    {
        return {
            initialVariationId: null
        };
    },

    computed:
    {
        /**
         * returns true if any variation has no attributes
         */
        hasEmptyOption()
        {
            return this.variations.some(variation => !variation.attributes.length);
        },

        /**
         * returns the variation, based on the selected attributes / unit
         * returns false if there are none or multiple results
         */
        currentSelection()
        {
            const filteredVariations = this.filterVariations(null, null, true);

            if (filteredVariations.length === 1)
            {
                return filteredVariations[0];
            }

            return false;
        },

        ...Vuex.mapState({
            attributes: state => state.variationSelect.attributes,
            currentVariation: state => state.item.variation.documents[0].data,
            selectedAttributes: state => state.variationSelect.selectedAttributes,
            selectedUnit: state => state.variationSelect.selectedUnit,
            units: state => state.variationSelect.units,
            variations: state => state.variationSelect.variations
        })
    },

    methods:
    {
        /**
         * select an attribute and check, if the selection is valid; if not, unsetInvalidSelection will be executed
         * @param {number} attributeId
         * @param {[number, string, null]} attributeValueId
         */
        selectAttribute(attributeId, attributeValueId)
        {
            attributeValueId = parseInt(attributeValueId) || null;
            this.$store.commit("selectItemAttribute", { attributeId, attributeValueId });
            this.onSelectionChange(attributeId, attributeValueId, null);
        },

        /**
         * select a unit and check, if the selection is valid; if not, unsetInvalidSelection will be executed
         * @param {[number, string]} unitId
         */
        selectUnit(unitId)
        {
            unitId = parseInt(unitId);
            this.$store.commit("selectItemUnit", unitId);
            this.onSelectionChange(null, null, unitId);
        },

        onSelectionChange(attributeId, attributeValueId, unitId)
        {
            if (this.currentSelection)
            {
                this.setVariation(this.currentSelection.variationId);
            }
            else
            {
                this.unsetInvalidSelection(attributeId, attributeValueId, unitId);
            }
        },

        unsetInvalidSelection(attributeId, attributeValueId, unitId)
        {
            let qualifiedVariations;

            if (isDefined(attributeValueId))
            {
                qualifiedVariations = this.variations.filter(variation =>
                {
                    return isDefined(variation.attributes.find(attribute =>
                        attribute.attributeId === attributeId && attribute.attributeValueId === attributeValueId));
                });
            }
            else if (isDefined(unitId))
            {
                qualifiedVariations = this.variations.filter(variation => variation.unitCombinationId === unitId);
            }
            else
            {
                // variations without attributes
                return;
            }

            let bestChoice;
            let numberOfRequiredChanges;

            for (const variation of qualifiedVariations)
            {
                let changes = 0;

                if (variation.unitCombinationId !== this.selectedUnit)
                {
                    changes++;
                }

                for (const attribute of variation.attributes)
                {
                    if (this.selectedAttributes[attribute.attributeId] !== attribute.attributeValueId)
                    {
                        changes++;
                    }
                }

                if (!numberOfRequiredChanges || changes < numberOfRequiredChanges)
                {
                    bestChoice = variation;
                    numberOfRequiredChanges = changes;
                }
            }

            const attributes = JSON.parse(JSON.stringify(this.selectedAttributes));
            const messages = [];
            const translationNotAvailable = TranslationService.translate("Ceres::Template.singleItemNotAvailable");

            for (const attribute of bestChoice.attributes)
            {
                if (this.selectedAttributes[attribute.attributeId] !== attribute.attributeValueId && this.selectedAttributes[attribute.attributeId] !== null)
                {
                    attributes[attribute.attributeId] = null;

                    const attributeToReset = this.attributes.find(attr => attr.attributeId === attribute.attributeId);

                    messages.push(
                        translationNotAvailable.replace("<name>", attributeToReset.name)
                    );
                }
            }
            if (bestChoice.unitCombinationId !== this.selectedUnit)
            {
                const translationContent = TranslationService.translate("Ceres::Template.singleItemContent");

                messages.push(translationNotAvailable.replace("<name>", translationContent));

                this.$store.commit("selectItemUnit", bestChoice.unitCombinationId);
            }

            this.$store.commit("setItemSelectedAttributes", attributes);

            if (this.currentSelection)
            {
                this.setVariation(this.currentSelection.variationId);
            }

            NotificationService.warn(
                messages.join("<br>")
            );
        },

        /**
         * returns matching variations with current selection
         * attributes and unitId could be filled, to check a specific selection
         * @param {object} attributes
         * @param {number} unitId
         * @param {boolean} strict
         */
        filterVariations(attributes, unitId, strict)
        {
            attributes = attributes || this.selectedAttributes;
            unitId = unitId || this.selectedUnit;

            const uniqueValues = [...new Set(Object.values(attributes))];
            const isEmptyOptionSelected = uniqueValues.length === 1 && isNull(uniqueValues[0]);

            return this.variations.filter(variation =>
            {
                // the selected unit is not matching
                if (variation.unitCombinationId !== unitId)
                {
                    return false;
                }

                // the variation has no attributes (only checked, if any attribute has a selected value); or the variation has attributes and empty option is selected
                if ((!isEmptyOptionSelected && !variation.attributes.length) || (isEmptyOptionSelected && variation.attributes.length))
                {
                    return false;
                }

                for (const attributeId in attributes)
                {
                    const variationAttribute = variation.attributes.find(variationAttribute =>
                        variationAttribute.attributeId === parseInt(attributeId));

                    // an attribute is not matching with selection
                    if (variationAttribute &&
                        variationAttribute.attributeValueId !== attributes[attributeId] &&
                        (strict || !strict && attributes[attributeId] !== null))
                    {
                        return false;
                    }
                }

                return true;
            });
        },

        /**
         * returns true, if the selection with a new attribute value would be valid
         * @param {number} attributeId
         * @param {[number, string, null]} attributeValueId
         */
        isAttributeSelectionValid(attributeId, attributeValueId)
        {
            attributeValueId = parseInt(attributeValueId) || null;
            if (this.selectedAttributes[attributeId] === attributeValueId)
            {
                return true;
            }

            const selectedAttributes = JSON.parse(JSON.stringify(this.selectedAttributes));

            selectedAttributes[attributeId] = parseInt(attributeValueId) || null;
            return !!this.filterVariations(selectedAttributes).length;
        },

        /**
         * returns true, if the selection with a new unitId would be valid
         * @param {[number, string]} unitId
         */
        isUnitSelectionValid(unitId)
        {
            unitId = parseInt(unitId);
            if (this.selectedUnit === unitId)
            {
                return true;
            }

            return !!this.filterVariations(null, unitId).length;
        },

        /**
         * dispatch vuex action 'loadVariation' to archive a variation
         * dispatches a custom event named 'onVariationChanged'
         * @param {[string, number, null]} variationId
         */
        setVariation(variationId)
        {
            if (!isDefined(variationId) && this.currentSelection)
            {
                variationId = this.currentSelection.variationId;
            }

            if (isDefined(variationId))
            {
                this.$store.dispatch("loadVariation", variationId).then(variation =>
                {
                    document.dispatchEvent(new CustomEvent("onVariationChanged",
                        {
                            detail:
                            {
                                attributes: variation.attributes,
                                documents: variation.documents
                            }
                        }));
                });
            }
        },

        isTextCut()
        {
            console.log("call isTextCut()");
            // TODO: prüft, ob der Name vom Attribute komplett angezeigt wird
        }
    },

    watch:
    {
        currentSelection(value)
        {
            this.$store.commit("setIsVariationSelected", !!value);
        }
    }
});
