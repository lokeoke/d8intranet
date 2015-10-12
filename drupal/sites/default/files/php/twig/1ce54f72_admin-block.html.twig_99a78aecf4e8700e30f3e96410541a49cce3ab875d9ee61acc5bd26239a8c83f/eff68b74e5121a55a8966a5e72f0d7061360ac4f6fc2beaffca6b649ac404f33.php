<?php

/* core/modules/system/templates/admin-block.html.twig */
class __TwigTemplate_8b982739c8bb4dd4652d17cf6a03200acb87ab2ad4e5afdd70f6c041a22d3eb1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 17
        echo "<div class=\"panel\">
  ";
        // line 18
        if ($this->getAttribute((isset($context["block"]) ? $context["block"] : null), "title", array())) {
            // line 19
            echo "    <h3 class=\"panel__title\">";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute((isset($context["block"]) ? $context["block"] : null), "title", array()), "html", null, true);
            echo "</h3>
  ";
        }
        // line 21
        echo "  ";
        if ($this->getAttribute((isset($context["block"]) ? $context["block"] : null), "content", array())) {
            // line 22
            echo "    <div class=\"panel__content\">";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute((isset($context["block"]) ? $context["block"] : null), "content", array()), "html", null, true);
            echo "</div>
  ";
        } elseif ($this->getAttribute(        // line 23
(isset($context["block"]) ? $context["block"] : null), "description", array())) {
            // line 24
            echo "    <div class=\"panel__description\">";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $this->getAttribute((isset($context["block"]) ? $context["block"] : null), "description", array()), "html", null, true);
            echo "</div>
  ";
        }
        // line 26
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/admin-block.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 26,  40 => 24,  38 => 23,  33 => 22,  30 => 21,  24 => 19,  22 => 18,  19 => 17,);
    }
}
/* {#*/
/* /***/
/*  * @file*/
/*  * Default theme implementation for an administrative block.*/
/*  **/
/*  * Available variables:*/
/*  * - block: An array of information about the block, including:*/
/*  *   - show: A flag indicating if the block should be displayed.*/
/*  *   - title: The block title.*/
/*  *   - content: (optional) The content of the block.*/
/*  *   - description: (optional) A description of the block.*/
/*  *     (Description should only be output if content is not available).*/
/*  **/
/*  * @ingroup themeable*/
/*  *//* */
/* #}*/
/* <div class="panel">*/
/*   {% if block.title %}*/
/*     <h3 class="panel__title">{{ block.title }}</h3>*/
/*   {% endif %}*/
/*   {% if block.content %}*/
/*     <div class="panel__content">{{ block.content }}</div>*/
/*   {% elseif block.description %}*/
/*     <div class="panel__description">{{ block.description }}</div>*/
/*   {% endif %}*/
/* </div>*/
/* */
