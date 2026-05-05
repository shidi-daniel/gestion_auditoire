<?php
/**
 * ============================================================
 * SGA — Fonctions PHP Procédurales (Q5–Q9 du TD)
 * Université Protestante au Congo · FCI 2025-2026
 * ============================================================
 */

/* ─── Q5 : Lecture des fichiers ─── */
function charger_salles($chemin_fichier) {
    if (!file_exists($chemin_fichier)) throw new Exception("Fichier salles introuvable : $chemin_fichier");
    $json = file_get_contents($chemin_fichier);
    $data = json_decode($json, true);
    if (!is_array($data)) throw new Exception("Format JSON invalide : salles.json");
    $salles = [];
    foreach ($data as $i => $s) {
        if (empty($s['id']) || empty($s['designation']) || !isset($s['capacite']))
            throw new Exception("Salle ligne $i : champ manquant (id, designation, capacite)");
        $salles[$s['id']] = ['id'=>$s['id'],'designation'=>$s['designation'],'capacite'=>(int)$s['capacite']];
    }
    return $salles;
}

function charger_promotions($chemin_fichier) {
    if (!file_exists($chemin_fichier)) throw new Exception("Fichier promotions introuvable : $chemin_fichier");
    $data = json_decode(file_get_contents($chemin_fichier), true);
    if (!is_array($data)) throw new Exception("Format JSON invalide : promotions.json");
    $promos = [];
    foreach ($data as $i => $p) {
        if (empty($p['id']) || !isset($p['effectif']))
            throw new Exception("Promotion ligne $i : champ manquant");
        $promos[$p['id']] = ['id'=>$p['id'],'libelle'=>$p['libelle']??$p['id'],'effectif'=>(int)$p['effectif']];
    }
    return $promos;
}

function charger_cours($chemin_fichier) {
    if (!file_exists($chemin_fichier)) throw new Exception("Fichier cours introuvable : $chemin_fichier");
    $data = json_decode(file_get_contents($chemin_fichier), true);
    if (!is_array($data)) throw new Exception("Format JSON invalide : cours.json");
    $cours = [];
    foreach ($data as $i => $c) {
        if (empty($c['id']) || empty($c['intitule']))
            throw new Exception("Cours ligne $i : champ manquant");
        $cours[$c['id']] = ['id'=>$c['id'],'intitule'=>$c['intitule'],'volume_horaire'=>(int)($c['volume_horaire']??4),'type'=>$c['type']??'tronc_commun','promotion'=>$c['promotion']??''];
    }
    return $cours;
}

function charger_options($chemin_fichier) {
    if (!file_exists($chemin_fichier)) throw new Exception("Fichier options introuvable : $chemin_fichier");
    $data = json_decode(file_get_contents($chemin_fichier), true);
    if (!is_array($data)) throw new Exception("Format JSON invalide : options.json");
    $options = [];
    foreach ($data as $i => $o) {
        if (empty($o['id'])) throw new Exception("Option ligne $i : id manquant");
        $options[$o['id']] = ['id'=>$o['id'],'libelle'=>$o['libelle']??$o['id'],'promotion_parent'=>$o['promotion_parent']??'','effectif'=>(int)($o['effectif']??0)];
    }
    return $options;
}

/* ─── Q6 : Vérification des contraintes ─── */
function salle_disponible($planning, $id_salle, $creneau) {
    $jour = $creneau['jour']; $hd = $creneau['heure_debut']; $hf = $creneau['heure_fin'];
    if (!isset($planning[$jour])) return true;
    foreach ($planning[$jour] as $a) {
        if ($a['id_salle'] === $id_salle && $a['heure_debut'] < $hf && $a['heure_fin'] > $hd) return false;
    }
    return true;
}

function capacite_suffisante($salles, $id_salle, $effectif) {
    return isset($salles[$id_salle]) && $salles[$id_salle]['capacite'] >= $effectif;
}

function creneau_libre_groupe($planning, $id_groupe, $creneau) {
    $jour = $creneau['jour']; $hd = $creneau['heure_debut']; $hf = $creneau['heure_fin'];
    if (!isset($planning[$jour])) return true;
    foreach ($planning[$jour] as $a) {
        if ($a['id_groupe'] === $id_groupe && $a['heure_debut'] < $hf && $a['heure_fin'] > $hd) return false;
    }
    return true;
}

/* ─── Q7 : Génération du planning ─── */
function creer_creneaux_disponibles() {
    $jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];
    $horaires = [['debut'=>8,'fin'=>12],['debut'=>13,'fin'=>17]];
    $creneaux = [];
    foreach ($jours as $j) foreach ($horaires as $h) $creneaux[] = ['jour'=>$j,'heure_debut'=>$h['debut'],'heure_fin'=>$h['fin']];
    return $creneaux;
}

function generer_planning($salles, $promotions, $cours, $options, $creneaux_disponibles) {
    $planning = [];
    $salles_tri = $salles;
    usort($salles_tri, fn($a,$b) => $b['capacite'] - $a['capacite']);

    // Tronc commun
    foreach ($cours as $c) {
        if ($c['type'] !== 'tronc_commun' || empty($c['promotion'])) continue;
        $promo_id = $c['promotion'];
        if (!isset($promotions[$promo_id])) continue;
        $effectif = $promotions[$promo_id]['effectif'];
        $nb = max(1, (int)ceil($c['volume_horaire'] / 4));
        $placed = 0;
        foreach ($creneaux_disponibles as $cr) {
            if ($placed >= $nb) break;
            foreach ($salles_tri as $salle) {
                if (!capacite_suffisante($salles, $salle['id'], $effectif)) continue;
                if (!salle_disponible($planning, $salle['id'], $cr)) continue;
                if (!creneau_libre_groupe($planning, $promo_id, $cr)) continue;
                $planning[$cr['jour']][] = ['id_cours'=>$c['id'],'id_groupe'=>$promo_id,'id_salle'=>$salle['id'],'heure_debut'=>$cr['heure_debut'],'heure_fin'=>$cr['heure_fin'],'jour'=>$cr['jour'],'effectif'=>$effectif,'type'=>'tronc_commun'];
                $placed++; break;
            }
        }
    }

    // Options
    foreach ($options as $opt) {
        $promo_id = $opt['promotion_parent'];
        if (!isset($promotions[$promo_id])) continue;
        $effectif = $opt['effectif'];
        $placed = 0;
        foreach ($creneaux_disponibles as $cr) {
            if ($placed >= 1) break;
            foreach ($salles_tri as $salle) {
                if (!capacite_suffisante($salles, $salle['id'], $effectif)) continue;
                if (!salle_disponible($planning, $salle['id'], $cr)) continue;
                if (!creneau_libre_groupe($planning, $opt['id'], $cr)) continue;
                $planning[$cr['jour']][] = ['id_cours'=>$opt['id'],'id_groupe'=>$opt['id'],'id_salle'=>$salle['id'],'heure_debut'=>$cr['heure_debut'],'heure_fin'=>$cr['heure_fin'],'jour'=>$cr['jour'],'effectif'=>$effectif,'type'=>'option'];
                $placed++; break;
            }
        }
    }
    return $planning;
}

/* ─── Q8 : Sauvegarde ─── */
function sauvegarder_planning($planning, $chemin_fichier) {
    $lignes = ["PLANNING HEBDOMADAIRE — SGA · UPC/FCI 2025-2026\n" . str_repeat('=',55)];
    $ordre = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];
    foreach ($ordre as $j) {
        if (!isset($planning[$j])) continue;
        $lignes[] = "\n$j :";
        foreach ($planning[$j] as $a) {
            $lignes[] = sprintf("  %02dh00-%02dh00 | %-12s | %-15s | %s", $a['heure_debut'],$a['heure_fin'],$a['id_salle'],$a['id_groupe'],$a['id_cours']);
        }
    }
    return file_put_contents($chemin_fichier, implode("\n",$lignes)) !== false;
}

function sauvegarder_planning_json($planning, $chemin_fichier) {
    return file_put_contents($chemin_fichier, json_encode($planning, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}

/* ─── Q9 : Rechargement & affichage ─── */
function charger_planning($chemin_fichier) {
    if (!file_exists($chemin_fichier)) throw new Exception("planning.json introuvable");
    $p = json_decode(file_get_contents($chemin_fichier), true);
    if (!is_array($p)) throw new Exception("Format planning.json invalide");
    return $p;
}

function afficher_planning_html($planning, $salles=[], $cours=[]) {
    $jours = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi'];
    $creneaux = [['debut'=>8,'fin'=>12,'label'=>'8h00 – 12h00'],['debut'=>13,'fin'=>17,'label'=>'13h00 – 17h00']];
    $colors = ['L1'=>'#4f46e5','L2'=>'#10b981','L3'=>'#f59e0b','L4'=>'#ef4444','default'=>'#7c3aed'];
    $html = '<style>
    .ptbl{width:100%;border-collapse:collapse;font-size:12px}
    .ptbl th{background:rgba(79,70,229,.18);color:#a5b4fc;padding:10px 12px;border:1px solid rgba(255,255,255,.08);font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.4px}
    .ptbl td{border:1px solid rgba(255,255,255,.07);padding:6px 8px;vertical-align:top;background:rgba(255,255,255,.015)}
    .ptbl td.cr-label{color:#a5b4fc;font-weight:700;font-size:11px;white-space:nowrap;background:rgba(79,70,229,.07);text-align:center}
    .slot{border-radius:7px;padding:6px 8px;margin:2px 0;border-left:3px solid;font-size:11.5px}
    .slot-cours{font-weight:700;font-size:11.5px;margin-bottom:2px}
    .slot-salle{font-size:10px;opacity:.7}
    .slot-grp{display:inline-block;padding:1px 5px;border-radius:4px;font-size:9.5px;font-weight:800;margin-top:2px}
    .empty-cell{color:rgba(148,163,184,.3);font-size:10px;text-align:center;padding:12px 0}
    </style>';
    $html .= '<table class="ptbl"><thead><tr><th>Créneau</th>';
    foreach ($jours as $j) $html .= "<th>$j</th>";
    $html .= '</tr></thead><tbody>';
    foreach ($creneaux as $cr) {
        $html .= '<tr><td class="cr-label">'.htmlspecialchars($cr['label']).'</td>';
        foreach ($jours as $j) {
            $html .= '<td>';
            $found = false;
            if (isset($planning[$j])) {
                foreach ($planning[$j] as $a) {
                    if ($a['heure_debut']==$cr['debut'] && $a['heure_fin']==$cr['fin']) {
                        $found = true;
                        $intitule = isset($cours[$a['id_cours']]) ? $cours[$a['id_cours']]['intitule'] : $a['id_cours'];
                        $grp = $a['id_groupe'];
                        $col = $colors[$grp] ?? $colors['default'];
                        $bg = $col.'22';
                        $html .= "<div class='slot' style='background:{$bg};border-color:{$col}'>";
                        $html .= "<div class='slot-cours' style='color:{$col}'>".htmlspecialchars($intitule)."</div>";
                        $html .= "<div class='slot-salle'>🏢 ".htmlspecialchars($a['id_salle'])."</div>";
                        $html .= "<span class='slot-grp' style='background:{$col}22;color:{$col}'>".htmlspecialchars($grp)."</span>";
                        $html .= '</div>';
                    }
                }
            }
            if (!$found) $html .= '<div class="empty-cell">–</div>';
            $html .= '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/* ─── B1 : Détection de conflits ─── */
function detecter_conflits($planning) {
    $conflits = [];
    foreach ($planning as $jour => $affectations) {
        for ($i=0;$i<count($affectations);$i++) {
            for ($j=$i+1;$j<count($affectations);$j++) {
                $a1=$affectations[$i]; $a2=$affectations[$j];
                $overlap = $a1['heure_debut']<$a2['heure_fin'] && $a1['heure_fin']>$a2['heure_debut'];
                if (!$overlap) continue;
                if ($a1['id_salle']===$a2['id_salle'])
                    $conflits[]=['type'=>'conflit_salle','jour'=>$jour,'heure_debut'=>$a1['heure_debut'],'heure_fin'=>$a1['heure_fin'],'salle'=>$a1['id_salle'],'cours1'=>$a1['id_cours'],'cours2'=>$a2['id_cours']];
                if ($a1['id_groupe']===$a2['id_groupe'])
                    $conflits[]=['type'=>'conflit_groupe','jour'=>$jour,'heure_debut'=>$a1['heure_debut'],'heure_fin'=>$a1['heure_fin'],'groupe'=>$a1['id_groupe'],'cours1'=>$a1['id_cours'],'cours2'=>$a2['id_cours']];
            }
        }
    }
    return $conflits;
}

/* ─── B2 : Rapport d'occupation ─── */
function generer_rapport_occupation($planning, $salles, $chemin_fichier) {
    $total_creneaux = 10;
    $rapport = ["RAPPORT D'OCCUPATION DES SALLES — SGA · UPC/FCI 2025-2026\n".str_repeat('=',60)];
    $occ = [];
    foreach ($planning as $j => $affs) foreach ($affs as $a) $occ[$a['id_salle']][] = $j.$a['heure_debut'];
    foreach ($salles as $id => $s) {
        $nb = isset($occ[$id]) ? count(array_unique($occ[$id])) : 0;
        $libres = $total_creneaux - $nb;
        $taux = round($nb/$total_creneaux*100,1);
        $rapport[] = "\nSalle : {$s['designation']} ({$id})\n  Occupés : $nb | Libres : $libres | Taux : $taux%";
    }
    file_put_contents($chemin_fichier, implode("\n",$rapport));
    return $rapport;
}
