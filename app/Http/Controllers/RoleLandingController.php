<?php

namespace App\Http\Controllers;

use App\Models\CreatorProfile;
use Illuminate\View\View;

class RoleLandingController extends Controller
{
    public function show(string $role): View
    {
        $pages = [
            'creator' => [
                'eyebrow' => 'A SPACE FOR YOUR ORIGINALITY',
                'headline' => 'Your creativity deserves to be discovered.',
                'description' => 'Build your professional creator portfolio, showcase your best work, and connect with brands looking for talent like yours.',
                'secondary' => 'Explore Opportunities',
                'image' => 'creative-studio.webp',
                'imageLabel' => 'Your next chapter starts here.',
                'section' => 'A portfolio that feels like you.',
                'intro' => 'Bring your best work together. Give potential collaborators a clear picture of your style, your services, and what you love to create.',
                'benefitsTitle' => 'Less friction. More room to create.',
                'benefits' => [
                    ['camera', 'Your work, beautifully presented', 'Build a public portfolio with original images, video links, and the story behind your work.'],
                    ['pin', 'Be discovered locally', 'Help businesses find you by city, category, platform, and the services you offer.'],
                    ['sparkles', 'Collaborate on your terms', 'Review briefs, agree on scope, propose changes, and track delivery in one place.'],
                ],
                'steps' => [['Make it yours', 'Create your account and add your city and creative category.'], ['Build your portfolio', 'Add your bio, original work, social accounts, and service packages.'], ['Get ready to collaborate', 'Consent to publication and submit your profile for review before it appears in discovery.']],
                'workflowTitle' => 'Your services. Your creative possibilities.',
                'workflow' => 'Define deliverables, pricing, turnaround times, and revisions in your service packages. Explore published campaign briefs or receive direct enquiries from brands and individuals. Agree on the details before starting work.',
                'faqs' => [['Who can join as a creator?', 'Influencers and UGC creators can build a profile and share original work they have permission to publish.'], ['When will my portfolio become public?', 'Verify your email, complete your profile, add a portfolio and service package, give publication consent, and submit it for review. Only approved profiles appear publicly.'], ['Can I choose which projects to accept?', 'Yes. Review each brief and its terms, accept it, decline it, or propose changes before work begins.']],
                'final' => 'Make space for your next opportunity.',
            ],
            'brand' => [
                'eyebrow' => 'LOCAL TALENT. MEANINGFUL CONNECTIONS.',
                'headline' => 'Find the right creators for your brand.',
                'description' => 'Discover talented influencers, compare their work, and build meaningful collaborations that connect with your audience.',
                'secondary' => 'Explore Creators',
                'image' => 'beauty-still-life.webp',
                'imageLabel' => 'A fresh perspective for your brand.',
                'section' => 'Discover the person behind the portfolio.',
                'intro' => 'Explore local influencers and UGC creators. Filter by city, category, or platform, compare original work, and save the people who fit your next campaign.',
                'benefitsTitle' => 'Thoughtful tools for better collaboration.',
                'benefits' => [
                    ['pin', 'Start close to home', 'Find local creators in Yamunanagar, Jagadhri, and beyond with location-based discovery.'],
                    ['camera', 'See the work first', 'Explore transparent portfolios, self-reported social information, and service packages before reaching out.'],
                    ['briefcase', 'Keep everyone aligned', 'Share campaign briefs, agree on deliverables, and follow each collaboration from request to review.'],
                ],
                'steps' => [['Introduce your business', 'Create a Brand account and complete your business profile.'], ['Find your creative fit', 'Browse portfolios, compare services, and save a shortlist.'], ['Start a conversation', 'Send a clear brief, confirm terms together, and track the work.']],
                'workflowTitle' => 'From campaign brief to final review.',
                'workflow' => 'Draft campaign briefs with your budget, deliverables, requirements, and deadlines. Submit campaigns for moderation, review creator applications, and keep accepted collaborations and deliverables together in your workspace.',
                'faqs' => [['Can I discover creators in my city?', 'Yes. Use location, category, and platform filters to narrow your search to relevant local talent.'], ['Can I contact a creator directly?', 'After verifying your email and completing your business profile, send a collaboration brief from an available creator’s profile.'], ['Does Colliva guarantee campaign results?', 'No. Review the creator’s original work and agree on scope and expectations together. Payments are coordinated directly between collaborators.']],
                'final' => 'Your next collaboration starts with a connection.',
            ],
            'customer' => [
                'eyebrow' => 'FOR THE IDEAS THAT ARE PERSONALLY YOURS',
                'headline' => 'Find creative talent for your next big idea.',
                'description' => 'Explore talented creators for events, personal campaigns, content creation, and special projects.',
                'secondary' => 'Browse Creators',
                'image' => 'creative-studio.webp',
                'imageLabel' => 'Something special. Made together.',
                'section' => 'Big moments. Personal projects. Fresh ideas.',
                'intro' => 'You don’t need to represent a business to work with a creator. Find a creative partner for the moments and projects that matter to you.',
                'benefitsTitle' => 'A little inspiration for your next project.',
                'benefits' => [
                    ['camera', 'Events worth remembering', 'Find a creator whose style suits your celebration, gathering, or special occasion.'],
                    ['heart', 'Ideas with a personal touch', 'Explore talent for a personal campaign, a passion project, or content for your own channels.'],
                    ['sparkles', 'Creative help, clearly agreed', 'Share your idea, agree on deliverables and timing, and review the finished work together.'],
                ],
                'steps' => [['Find your inspiration', 'Browse original portfolios and save your favourite creators.'], ['Share your idea', 'Create a Customer account, verify your email, and send a personal project enquiry.'], ['Make it happen together', 'Agree on the scope, budget, and deadline, then follow your collaboration through to delivery.']],
                'workflowTitle' => 'Your project, without the business paperwork.',
                'workflow' => 'Keep a personal shortlist and send enquiries directly to available creators. Your Customer space keeps your requests, active collaborations, and account settings close at hand, without business profiles or campaign publishing.',
                'faqs' => [['Do I need a business to join?', 'No. Customer accounts are designed for individuals hiring creators for personal projects.'], ['What should I include in an enquiry?', 'Describe your idea, deliverables, intended use, budget, deadline, and payment terms so the creator can decide whether it is a fit.'], ['How are payments handled?', 'You and the creator coordinate payments directly. Colliva does not collect payments or provide escrow.']],
                'final' => 'Bring that idea to life.',
            ],
        ];

        abort_unless(isset($pages[$role]), 404);

        return view('role-landing', ['role' => $role, 'page' => $pages[$role], 'creators' => CreatorProfile::publiclyVisible()->with(['location', 'category', 'socialAccounts', 'portfolioItems' => fn ($query) => $query->where('hidden', false)])->withMin('servicePackages', 'price_minor')->latest('id')->limit(3)->get()]);
    }
}
