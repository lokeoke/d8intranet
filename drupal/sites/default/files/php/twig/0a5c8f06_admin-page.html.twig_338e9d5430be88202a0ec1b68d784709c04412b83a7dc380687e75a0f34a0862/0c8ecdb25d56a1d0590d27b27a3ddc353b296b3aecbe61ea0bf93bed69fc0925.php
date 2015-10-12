<?php

/* core/modules/system/templates/admin-page.html.twig */
class __TwigTemplate_39a5cb68f4853a0ef7aeb7f328995ac4d4624c45efe54a889b00ac607e588ad6 extends Twig_Template
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
        echo "
<div class=\"clearfix\">
  ";
        // line 19
        echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, (isset($context["system_compact_link"]) ? $context["system_compact_link"] : null), "html", null, true);
        echo "
  ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["containers"]) ? $context["containers"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["container"]) {
            // line 21
            echo "    <div class=\"layout-column layout-column--half\">
      ";
            // line 22
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["container"], "blocks", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["block"]) {
                // line 23
                echo "        ";
                echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, $context["block"], "html", null, true);
                echo "
      ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['block'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 25
            echo "    </div>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['container'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "core/modules/system/templates/admin-page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 27,  47 => 25,  38 => 23,  34 => 22,  31 => 21,  27 => 20,  23 => 19,  19 => 17,);
    }
}
/* {#*/
/* /***/
/*  * @file*/
/*  * Default theme implementation for an administrative page.*/
/*  **/
/*  * Available variables:*/
/*  * - system_compact_link: Themed link to toggle compact view.*/
/*  * - containers: An list of administrative blocks keyed by position: left or*/
/*  *   right. Contains:*/
/*  *   - blocks: A list of blocks within a container.*/
/*  **/
/*  * @see template_preprocess_admin_page()*/
/*  **/
/*  * @ingroup themeable*/
/*  *//* */
/* #}*/
/* */
/* <div class="clearfix">*/
/*   {{ system_compact_link }}*/
/*   {% for container in containers %}*/
/*     <div class="layout-column layout-column--half">*/
/*       {% for block in container.blocks %}*/
/*         {{ block }}*/
/*       {% endfor %}*/
/*     </div>*/
/*   {% endfor %}*/
/* </div>*/
/* */
