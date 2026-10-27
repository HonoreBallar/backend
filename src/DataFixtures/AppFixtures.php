<?php

namespace App\DataFixtures;

use App\Entity\Expertise;
use App\Entity\PipelineStep;
use App\Entity\Project;
use App\Entity\SiteSettings;
use App\Entity\SocialLink;
use App\Entity\Stat;
use App\Entity\Technology;
use App\Entity\TimelineEntry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Initial content, copied from frontend/src/lib/data.ts.
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist((new SiteSettings())
            ->setContactEmail('amounahonore@gmail.com')
            ->setRoles(['Développeur IA full-stack', 'Intégrateur LLM & RAG', 'Ingénieur DevOps', 'Développeur web & mobile']));

        foreach ([
            ['3+', "ans d'expérience"],
            ['5', 'projets majeurs livrés'],
            ['4', 'certifications IBM & Coursera'],
        ] as $i => [$value, $label]) {
            $manager->persist((new Stat())->setValue($value)->setLabel($label)->setPosition($i));
        }

        foreach ([
            ['Python', '#facc15'], ['FastAPI', '#14b8a6'], ['LLM / RAG', '#a78bfa'], ['Next.js', '#f4f6fb'],
            ['TypeScript', '#3b82f6'], ['Angular', '#f87171'], ['React Native', '#38bdf8'], ['Expo', '#f4f6fb'],
            ['Java', '#f97316'], ['Spring Boot', '#34d399'], ['PHP', '#818cf8'], ['Laravel', '#f87171'],
            ['Symfony', '#f4f6fb'], ['PostgreSQL', '#60a5fa'], ['Docker', '#0ea5e9'], ['GitHub Actions', '#f4f6fb'], ['Nginx', '#34d399'],
        ] as $i => [$name, $color]) {
            $manager->persist((new Technology())->setName($name)->setColor($color)->setPosition($i));
        }

        foreach ([
            ['Fullstack web & mobile', "Applications web et mobiles de bout en bout : conception d'API REST, back-ends Spring Boot, Laravel ou FastAPI, interfaces Angular et Next.js, applications React Native publiées sur l'App Store et Google Play.", ['Spring Boot / Laravel', 'Angular / Next.js', 'React Native'], 'code', '#22d3ee'],
            ['Intelligence artificielle', "Systèmes RAG en production pour interroger un corpus documentaire en langage naturel : ingestion, chunking, embeddings, recherche sémantique et intégration de LLM via API. Conception d'agents IA en cours.", ['LLM / RAG', 'Python / FastAPI', 'Embeddings'], 'chip', '#a78bfa'],
            ['DevOps & déploiement', "Conteneurisation Docker, pipelines CI/CD avec GitHub Actions, Nginx et déploiement automatisé des applications, avec une attention particulière à la fiabilité et à l'observabilité.", ['Docker', 'GitHub Actions', 'Nginx'], 'cloud', '#34d399'],
        ] as $i => [$title, $text, $tags, $icon, $color]) {
            $manager->persist((new Expertise())
                ->setTitle($title)->setText($text)->setTags($tags)->setIcon($icon)->setColor($color)->setPosition($i));
        }

        foreach ([
            ['Cockpit 360 — Assistant IA décisionnel', 'Neurones Technologies · Système RAG mis en production pour interroger un corpus documentaire en langage naturel : ingestion, chunking, embeddings et base vectorielle, LLM intégrés via API, conteneurisé avec Docker et livré en CI/CD GitHub Actions.', 'IA', ['Python', 'RAG / LLM', 'FastAPI', 'Next.js', 'Docker'], '139, 92, 246'],
            ['Entrée en relation bancaire — BACI', "Neurones Technologies · Digitalisation du parcours d'ouverture de compte de Banque Atlantique Côte d'Ivoire : validation documentaire, traitement des dossiers, sécurisation des échanges et gestion des rôles utilisateurs.", 'Fullstack', ['Spring Boot', 'Angular', 'API REST'], '14, 165, 233'],
            ['Diamondock — App mobile e-commerce', "JARS Technologies · Application iOS et Android publiée sur l'App Store et Google Play : catalogue produits, panier, suivi des commandes, authentification et intégration des API backend.", 'Fullstack', ['React Native', 'Expo', 'API REST'], '16, 185, 129'],
            ['Winimall — Marketplace SaaS', "JARS Technologies · Plateforme multi-vendeurs : gestion des boutiques, des produits et des commandes. Conception et développement de l'API, du front et de leur intégration.", 'Fullstack', ['Laravel', 'API REST', 'SQL', 'Bootstrap'], '236, 72, 153'],
            ['YEQUA — Réservation hôtelière', "JUNI-CORE SARL · Développement backend d'une plateforme proposant plus de 1 000 chambres d'hôtels, résidences et appartements en Côte d'Ivoire : gestion des professionnels et des hôtels, des réservations et du back-office.", 'Fullstack', ['PHP', 'Symfony', 'Bootstrap'], '245, 158, 11'],
        ] as $i => [$title, $description, $category, $tags, $glow]) {
            $manager->persist((new Project())
                ->setTitle($title)->setDescription($description)->setCategory($category)
                ->setTags($tags)->setGlow($glow)->setPosition($i));
        }

        foreach ([
            ['01', 'Code', 'Revue de code, Clean Code, tests', '#22d3ee'],
            ['02', 'Build', 'Images Docker reproductibles', '#60a5fa'],
            ['03', 'Test', 'Tests automatisés dans GitHub Actions', '#a78bfa'],
            ['04', 'Deploy', 'Docker Compose, Nginx, déploiement automatisé', '#34d399'],
            ['05', 'Monitor', 'Logs, métriques et observabilité', '#fbbf24'],
        ] as $i => [$number, $title, $text, $color]) {
            $manager->persist((new PipelineStep())
                ->setNumber($number)->setTitle($title)->setText($text)->setColor($color)->setPosition($i));
        }

        foreach ([
            ["Juin 2025 — Aujourd'hui", 'Développeur IA & DevOps · Neurones Technologies', "Mise en production d'un système RAG (Cockpit 360), digitalisation de l'entrée en relation bancaire pour Banque Atlantique (Spring Boot, Angular) et déploiement automatisé des applications."],
            ['Oct. 2023 — Mai 2025', 'Développeur web & mobile · JARS Technologies', "Développement et publication de l'application React Native Diamondock sur iOS et Android, et participation à la marketplace SaaS Winimall (Laravel)."],
            ['Févr. 2023 — Sept. 2023', 'Développeur full-stack web · SOFTSYS', "Conception et intégration d'API reliant les interfaces utilisateur aux services métier, et optimisation du référencement naturel (SEO)."],
            ['2026', 'Certifications IBM · Coursera', 'IBM RAG and Agentic AI, IBM DevOps, Cloud and Agile Foundations, IBM Front-End Developer, et Mobile App Development, UX and Publishing (SkillUp).'],
            ['2014 — 2019', 'Licence pro Génie Logiciel · CESA Plateau', "Licence professionnelle en Génie Logiciel (2019), après un BTS Informatique développeur d'application (2015)."],
        ] as $i => [$period, $title, $text]) {
            $manager->persist((new TimelineEntry())->setPeriod($period)->setTitle($title)->setText($text)->setPosition($i));
        }

        foreach ([
            ['GitHub', 'https://github.com/HonoreBallar'],
            ['LinkedIn', 'https://www.linkedin.com/in/honor%C3%A9-amounan-38816913a'],
        ] as $i => [$label, $href]) {
            $manager->persist((new SocialLink())->setLabel($label)->setHref($href)->setPosition($i));
        }

        $manager->flush();
    }
}
