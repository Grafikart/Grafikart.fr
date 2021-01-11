<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use App\Domain\Premium\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class TransactionImporter implements TypeImporterInterface
{
    use DatabaseImporterTools;

    private const WINDOW = 1000;
    private \PDO $pdo;
    private EntityManagerInterface $em;

    public function __construct(\PDO $pdo, EntityManagerInterface $em)
    {
        $this->pdo = $pdo;
        $this->em = $em;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->truncate('transaction');
        $offset = 0;
        $io->title('Importation des transactions');
        $query = $this->pdo->prepare('SELECT COUNT(id) as count FROM transactions');
        $query->execute();
        $result = $query->fetch();
        $io->progressStart($result['count']);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $oldTransaction = [
            'id' => 0,
        ];
        $window = self::WINDOW;
        while (true) {
            $query = $this->pdo->prepare(<<<SQL
            SELECT id, user_id, ref, ref_id, created_at, price, tax, method, paypal_id, reimbursed
            FROM transactions
            WHERE ref = 'premium'
            ORDER BY id ASC
            LIMIT $offset, $window
            SQL
            );
            $query->execute();
            /** @var array<string,mixed> $oldTransactions */
            $oldTransactions = $query->fetchAll();
            if (empty($oldTransactions)) {
                break;
            }
            foreach ($oldTransactions as $oldTransaction) {
                $user = $this->em->getRepository(User::class)->find($oldTransaction['user_id']);
                $io->progressAdvance();
                if (!$user) {
                    continue;
                }
                $legacyDuration = 2 === $oldTransaction['price'] ? 1 : 6;
                $transaction = (new Transaction())
                    ->setId($oldTransaction['id'])
                    ->setAuthor($user)
                    ->setCreatedAt(new \DateTimeImmutable($oldTransaction['created_at']))
                    ->setPrice($oldTransaction['price'])
                    ->setTax(floatval($oldTransaction['tax'] ?: 0))
                    ->setMethod($oldTransaction['method'])
                    ->setRefunded($oldTransaction['reimbursed'])
                    ->setMethodRef($oldTransaction['paypal_id'])
                    ->setDuration(((int) $oldTransaction['ref_id']) ?: $legacyDuration);
                $this->em->persist($transaction);
                $this->disableAutoIncrement($transaction);
            }
            $this->em->flush();
            $this->em->clear();
            $offset += self::WINDOW;
        }
        $id = $oldTransaction['id'] + 1;
        $this->em->getConnection()->exec("ALTER SEQUENCE transaction_id_seq RESTART WITH $id;");
        $this->em->getConnection()->exec('REINDEX table transaction;');
        $io->progressFinish();
        $io->success(sprintf('Importation de %d transactions', $result['count']));
    }

    public function support(string $type): bool
    {
        return 'transactions' === $type;
    }
}
