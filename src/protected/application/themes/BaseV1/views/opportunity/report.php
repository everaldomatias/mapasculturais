<?php
use MapasCulturais\Entities\Registration as R;
use MapasCulturais\Entities\Agent;
use MapasCulturais\i;

function echoStatus($registration){
    switch ($registration->status){
        case R::STATUS_APPROVED:
            i::_e('selecionada');
            break;

        case R::STATUS_NOTAPPROVED:
            i::_e('não selecionada');
            break;

        case R::STATUS_WAITLIST:
            i::_e('suplente');
            break;

        case R::STATUS_INVALID:
            i::_e('inválida');
            break;

        case R::STATUS_SENT:
            i::_e('pendente');
            break;
    }
}

$_properties = $app->config['registration.propertiesToExport'];

?>
<style>
    tbody td, table th{
        text-align: left !important;
        border:1px solid black !important;
    }
</style>
<table>
    <thead>
        <tr>
            <th><?php i::_e("Número") ?></th>
            <?php if($entity->projectName): ?>
                <th><?php i::_e("Nome do projeto") ?></th>
            <?php endif; ?>
            <th><?php i::_e("Avaliação") ?></th>
            <th><?php i::_e("Status") ?></th>
            <?php if($entity->registrationCategories):?>
                <th><?php echo $entity->registrationCategTitle ?></th>
            <?php endif; ?>
                
            <?php foreach($entity->registrationFieldConfigurations as $field): ?>
                <th><?php echo $field->title; ?></th>
            <?php endforeach; ?>
            
            <th><?php i::_e('Arquivos') ?></th>
            <?php foreach($entity->getUsedAgentRelations() as $def): ?>
                <th><?php echo $def->label; ?></th>
                
                <th><?php echo $def->label; ?> - <?php i::_e("Área de Atuação") ?></th>
                
                <?php foreach($_properties as $prop): if($prop === 'name') continue; ?>
                    <th><?php echo $def->label; ?> - <?php echo Agent::getPropertyLabel($prop); ?></th>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($entity->sentRegistrations as $r): ?>
            <tr>
                <td><a href="<?php echo $r->singleUrl; ?>" target="_blank"><?php echo $r->number; ?></a></td>
                <?php if($entity->projectName): ?>
                    <td><?php echo $r->projectName ?></td>
                <?php endif; ?>
                <td><?php echo $r->getEvaluationResultString(); ?></td>
                <td><?php echoStatus($r); ?></td>

                <?php if($entity->registrationCategories):?>
                    <td><?php echo $r->category; ?></td>
                <?php endif; ?>
                    
                <?php foreach($entity->registrationFieldConfigurations as $field): $field_name = $field->getFieldName(); ?>
                    <?php if(is_array($r->$field_name)): ?>
                        <th><?php echo implode(', ', $r->$field_name); ?></th>
                    <?php else: ?>
                        <th><?php echo $r->$field_name; ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>

                <td>
                    <?php if(key_exists('zipArchive', $r->files)): ?>
                        <a href="<?php echo $r->files['zipArchive']->url; ?>"><?php i::_e("zip");?></a>
                     <?php endif; ?>
                </td>

                <?php
                foreach($r->_getDefinitionsWithAgents() as $def):
                    if($def->use == 'dontUse') continue;
                    $agent = $def->agent;
                ?>

                    <?php if($agent): ?>
                        <td><a href="<?php echo $agent->singleUrl; ?>" target="_blank"><?php echo $r->agentsData[$def->agentRelationGroupName]['name'];?></a></td>
                        
                        <td><?php echo implode(', ', $agent->terms['area']); ?></td>

                        <?php
                        foreach($_properties as $prop):
                            if($prop === 'name') continue;
                        $val = isset($r->agentsData[$def->agentRelationGroupName][$prop]) ? $r->agentsData[$def->agentRelationGroupName][$prop] : '';
                        ?>
                        <td><?php echo $prop === 'location' ? "{$val['latitude']},{$val['longitude']}" : $val ?></td>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <?php echo str_repeat('<td></td>', count($_properties)) ?>
                    <?php endif; ?>

                <?php endforeach ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>